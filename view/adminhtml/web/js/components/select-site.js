define([
    'Magento_Ui/js/form/element/ui-select',
    'ko',
    'jquery'
], function (Select, ko, $) {
    'use strict';

    return Select.extend({
        editorLink: ko.observable(),

        initialize: function () {
            this._super();

            this.loadEditorLink();

            return this;
        },
        /**
         * Parse data and set it to options.
         *
         * @param {Object} data - Response data object.
         * @returns {Object}
         */
        setParsed: function (data) {
            var option = this.parseData(data);
            if (data.error) {
                return this;
            }
            this.options([]);
            this.setOption(option);
            this.set('newOption', option);
        },
        /**
         * Normalize option object.
         *
         * @param {Object} data - Option object.
         * @returns {Object}
         */
        parseData: function (data) {
            return {
                value: data.customer.entity_id,
                label: data.customer.name
            };
        },

        toggleOptionSelected: function (data) {
            var isSelected = this.isSelected(data.value);

            if (this.lastSelectable && data.hasOwnProperty(this.separator)) {
                return this;
            }

            if (!this.multiple) {
                if (!isSelected) {
                    this.value(data.value);
                }
                this.listVisible(false);
            } else {
                if (!isSelected) { /*eslint no-lonely-if: 0*/
                    this.value.push(data.value);
                } else {
                    this.value(_.without(this.value(), data.value));
                }
            }

            this.loadEditorLink();

            return this;
        },

        loadEditorLink: function() {
            const [site_id, page_id] = this.value().split("_");

            const auto_login_url = window.userlogintoken ? `&autologin_code=${window.userlogintoken}` : '';

            const url = `${window.wordpressurl}wp-admin/post.php?post=${page_id}&action=edit&magento-referer=${window.location.href}${auto_login_url}`;

            this.editorLink(url);
        },

        openEditor: function() {
            window.open(this.editorLink(), 'WPCI Editor');
        }
    });
});
