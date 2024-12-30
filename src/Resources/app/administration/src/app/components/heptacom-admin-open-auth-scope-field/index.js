import './heptacom-admin-open-auth-scope-field.scss';
import template from './heptacom-admin-open-auth-scope-field.html.twig';

/**
 * @deprecated tag:v8.0.0 - Will be removed in v9.0.0. `sw-tagged-field` is used instead.
 */
export default {
    inheritAttrs: false,

    template,

    props: {
        value: {
            required: true,
            type: Array
        },
        defaultScopes: {
            required: false,
            type: Array,
            default() {
                return [];
            }
        }
    },

    data() {
        return {
            innerValue: this.value
                .filter(name => this.defaultScopes.indexOf(name) === -1)
                .map(name => ({ name })),
        }
    },

    watch: {
        value(newValue) {
            this.items = newValue;
        }
    },

    computed: {
        defaultScopeItems() {
            return this.defaultScopes.map(innerValue => ({
                name: innerValue,
            }));
        },

        items: {
            get() {
                return this.innerValue.map(object => object.name);
            },

            set(value) {
                this.innerValue = value.map(name => ({ name }));
            }
        }
    },

    methods: {
        addItem(name) {
            if (this.isDefaultScope(name)) {
                return;
            }

            this.innerValue = this.innerValue.filter(item => item.name !== name);
            this.innerValue.push({ name });
            this.$emit('input', this.items);
        },

        removeItem(name) {
            this.innerValue = this.innerValue.filter(item => item.name !== name);
            this.$emit('input', this.items);
        },

        exceptInput(object) {
            if (!(object && object.hasOwnProperty)) {
                return object;
            }

            return Object.keys(object).reduce((result, key) => {
                if (key !== 'input') {
                    result[key] = object[key]
                }

                return result
            }, {});
        },

        isDefaultScope(name) {
            return this.defaultScopes.findIndex(item => item === name) !== -1;
        }
    }
};
