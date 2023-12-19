/**
 * Copied from Shopware
 * @see shopware/administration/Resources/app/administration/src/app/service/rule-condition.service.ts
 *
 * This file also includes features that are not supported by this plugin.
 * To improve maintainability, this is not removed from the file.
 * The features that are not supported are caught in the backend during processing.
 */

type appScriptCondition = {
    id: string,
    config: unknown
}

type condition = {
    type: string,
    component: string,
    label: string,
    scopes: string[],
    group: string,
    scriptId: string,
    appScriptCondition: appScriptCondition,
}

type script = {
    id: string,
    name?: string,
    translated?: {
        name?: string,
    },
    group: string,
    config: unknown,
}

type operatorSetIdentifier =
    'defaultSet' |
    'singleStore' |
    'multiStore' |
    'string' |
    'bool' |
    'number' |
    'date' |
    'isNet' |
    'empty' |
    'zipCode';

type component = {
    type: string,
    config: {
        componentName: string,
    },
}

type moduleType = {
    id: string,
    name: string,
}

type group = {
    id: string,
    name: string,
}

type awarenessConfiguration = {
    notEquals?: Array<string>,
    equalsAny?: Array<string>,
    snippet?: string,
}

export class HeptacomOauthRuleDataProviderService {
    $store: { [key: string]: condition} = {};

    operators = {
        lowerThanEquals: {
            identifier: '<=',
            label: 'global.sw-condition.operator.lowerThanEquals',
        },
        equals: {
            identifier: '=',
            label: 'global.sw-condition.operator.equals',
        },
        greaterThanEquals: {
            identifier: '>=',
            label: 'global.sw-condition.operator.greaterThanEquals',
        },
        notEquals: {
            identifier: '!=',
            label: 'global.sw-condition.operator.notEquals',
        },
        greaterThan: {
            identifier: '>',
            label: 'global.sw-condition.operator.greaterThan',
        },
        lowerThan: {
            identifier: '<',
            label: 'global.sw-condition.operator.lowerThan',
        },
        isOneOf: {
            identifier: '=',
            label: 'global.sw-condition.operator.isOneOf',
        },
        isNoneOf: {
            identifier: '!=',
            label: 'global.sw-condition.operator.isNoneOf',
        },
        gross: {
            identifier: false,
            label: 'global.sw-condition.operator.gross',
        },
        net: {
            identifier: true,
            label: 'global.sw-condition.operator.net',
        },
        empty: {
            identifier: 'empty',
            label: 'global.sw-condition.operator.empty',
        },
    };

    operatorSets = {
        defaultSet: [
            this.operators.equals,
            this.operators.notEquals,
            this.operators.greaterThanEquals,
            this.operators.lowerThanEquals,
        ],
        singleStore: [
            this.operators.equals,
            this.operators.notEquals,
        ],
        multiStore: [
            this.operators.isOneOf,
            this.operators.isNoneOf,
        ],
        string: [
            this.operators.equals,
            this.operators.notEquals,
        ],
        bool: [
            this.operators.equals,
        ],
        number: [
            this.operators.equals,
            this.operators.greaterThan,
            this.operators.greaterThanEquals,
            this.operators.lowerThan,
            this.operators.lowerThanEquals,
            this.operators.notEquals,
        ],
        date: [
            this.operators.equals,
            this.operators.greaterThan,
            this.operators.greaterThanEquals,
            this.operators.lowerThan,
            this.operators.lowerThanEquals,
            this.operators.notEquals,
        ],
        isNet: [
            this.operators.gross,
            this.operators.net,
        ],
        empty: [
            this.operators.empty,
        ],
        zipCode: [
            this.operators.greaterThan,
            this.operators.greaterThanEquals,
            this.operators.lowerThan,
            this.operators.lowerThanEquals,
        ],
    };

    groups: { [key: string]: group} = {
        general: {
            id: 'general',
            name: 'sw-settings-rule.detail.groups.general',
        },
        user: {
            id: 'user',
            name: 'heptacom-admin-open-auth.condition.group.user',
        },
    };

    getByType(type: string): condition {
        if (!type) {
            return this.getByType('placeholder');
        }

        if (type === 'scriptRule') {
            const scriptRule = this.getConditions().filter((condition) => {
                return condition.type === 'scriptRule';
            }).shift();

            if (scriptRule) {
                return scriptRule;
            }
        }

        return this.$store[type];
    }

    getOperatorSet(operatorSetName: operatorSetIdentifier) {
        return this.operatorSets[operatorSetName];
    }

    addEmptyOperatorToOperatorSet(operatorSet: Array<unknown>) {
        return operatorSet.concat(this.operatorSets.empty);
    }

    getOperatorSetByComponent(component: component) {
        const componentName = component.config.componentName;
        const type = component.type;

        if (componentName === 'sw-single-select') {
            return this.operatorSets.singleStore;
        }
        if (componentName === 'sw-multi-select') {
            return this.operatorSets.multiStore;
        }
        if (type === 'bool') {
            return this.operatorSets.bool;
        }
        if (type === 'text') {
            return this.operatorSets.string;
        }
        if (type === 'int') {
            return this.operatorSets.number;
        }

        return this.operatorSets.defaultSet;
    }

    getOperatorOptionsByIdentifiers(identifiers: Array<string>, isMatchAny = false) {
        return identifiers.map((identifier) => {
            // @ts-ignore
            const option = Object.entries(this.operators).find(([name, operator]) => {
                // @ts-ignore
                if (isMatchAny && ['equals', 'notEquals'].includes(name)) {
                    return false;
                }
                // @ts-ignore
                if (!isMatchAny && ['isOneOf', 'isNoneOf'].includes(name)) {
                    return false;
                }

                return identifier === operator.identifier;
            });

            if (option) {
                return option.pop();
            }

            return {
                identifier,
                label: `global.sw-condition.operator.${identifier}`,
            };
        });
    }

    getByGroup(group: string) {
        // @ts-ignore
        const values = Object.values(this.$store);
        const conditions: Array<condition> = [];

        values.forEach(condition => {
            if (condition.group === group) {
                conditions.push(condition);
            }
        });

        return conditions;
    }

    getGroups() {
        return this.groups;
    }

    upsertGroup(groupName: string, groupData: group) {
        this.groups[groupName] = { ...this.groups[groupName], ...groupData };
    }

    removeGroup(groupName: string) {
        delete this.groups[groupName];
    }

    addCondition(type: string, condition: Partial<Omit<condition, 'type'>>) {
        (condition as condition).type = type;

        this.$store[condition.scriptId ?? type] = condition as condition;
    }

    getConditions(allowedScopes: Array<string>|null = null): condition[] {
        // @ts-ignore
        let values = Object.values(this.$store);

        if (allowedScopes !== null) {
            values = values.filter(condition => {
                return allowedScopes.some(scope => condition.scopes.indexOf(scope) !== -1);
            });
        }

        return values;
    }

    getComponentByCondition(condition: condition) {
        if (this.isAndContainer(condition)) {
            return 'sw-condition-and-container';
        }

        if (this.isOrContainer(condition)) {
            return 'sw-condition-or-container';
        }

        if (this.isAllLineItemsContainer(condition)) {
            return 'sw-condition-all-line-items-container';
        }

        if (!condition.type) {
            return 'sw-condition-base';
        }

        const conditionType = this.getByType(condition.type);

        if (typeof conditionType === 'undefined' || !conditionType.component) {
            return 'sw-condition-not-found';
        }

        return conditionType.component;
    }

    getAndContainerData() {
        return { type: 'andContainer', value: {} };
    }

    isAndContainer(condition: condition) {
        return condition.type === 'andContainer';
    }

    getOrContainerData() {
        return { type: 'orContainer', value: {} };
    }

    isOrContainer(condition: condition) {
        return condition.type === 'orContainer';
    }

    getPlaceholderData() {
        return { type: null, value: {} };
    }

    isAllLineItemsContainer(condition: condition) {
        return condition.type === 'allLineItemsContainer';
    }
}
