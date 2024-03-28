export default {
    methods: {
        validateCurrentValue(value) {
            const url = this.getURLInstance(value);

            // If the input is invalid, no URL can be constructed
            if (!url) {
                return null;
            }

            // build URL via native URL.toString() function instead by hand @see NEXT-15747
            return url
                .toString()
                .replace(/([a-zA-Z0-9]+\:\/\/)+/, '')
                .replace(url.host, this.unicodeUriFilter(url.host));
        },
    }
};
