/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var MailListController = (function () {
        function MailListController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.actions = function (item) {
                var gettext = _this.gettext;
                var actions = [
                    { 'text': gettext('Edit..'), 'icon': 'fa-edit', 'hint': gettext('Edit mail'), 'href': '/admin/mails/edit/' + item.mail_id },
                    { 'text': gettext('Clone'), 'icon': 'fa-copy', 'hint': gettext('Clone mail'), 'click': 'ctrl.clone(item)' },
                    { 'text': gettext('Remove'), 'icon': 'fa-trash', 'hint': gettext('Delete this mail'), 'click': 'item.removeConfirm("Removed")' },
                ];
                _this.$ui.bottomSheet(actions, gettext('Actions for: ') + item.name, _this.$scope, { item: item, ctrl: _this });
            };
            this.clone = function (mail) {
                var gettext = _this.gettext;
                mail.contents.setItemsPerPage(99, false);
                mail.contents.reloadAll(true).then(function () {
                    _this.$ui.prompt(gettext('Enter new mail name'), gettext('new-name')).then(function (name) {
                        mail.clone().attr('name', name).save(gettext('Mail duplicated')).then(function (copy) {
                            angular.forEach(mail.contents, function (content) { return copy.item.contents.cloneItem(content).save(); });
                        });
                    });
                });
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
        }
        return MailListController;
    }());
    Admin.MailListController = MailListController;
    angular.module('mailListApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('mailListController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', MailListController]);
})(Admin || (Admin = {}));
