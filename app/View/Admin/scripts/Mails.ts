/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class MailListController {
        constructor(public $scope:any, public $minute:any, public $ui:any, public $timeout:ng.ITimeoutService,
                    public gettext:angular.gettext.gettextFunction, public gettextCatalog:angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
        }

        actions = (item) => {
            let gettext = this.gettext;
            let actions = [
                {'text': gettext('Edit..'), 'icon': 'fa-edit', 'hint': gettext('Edit mail'), 'href': '/admin/mails/edit/' + item.mail_id},
                {'text': gettext('Clone'), 'icon': 'fa-copy', 'hint': gettext('Clone mail'), 'click': 'ctrl.clone(item)'},
                {'text': gettext('Remove'), 'icon': 'fa-trash', 'hint': gettext('Delete this mail'), 'click': 'item.removeConfirm("Removed")'},
            ];

            this.$ui.bottomSheet(actions, gettext('Actions for: ') + item.name, this.$scope, {item: item, ctrl: this});
        };

        clone = (mail) => {
            let gettext = this.gettext;

            mail.contents.setItemsPerPage(99, false);
            mail.contents.reloadAll(true).then(() => {
                this.$ui.prompt(gettext('Enter new mail name'), gettext('new-name')).then(function (name) {
                    mail.clone().attr('name', name).save(gettext('Mail duplicated')).then(function (copy) {
                        angular.forEach(mail.contents, (content) => copy.item.contents.cloneItem(content).save());
                    });
                });
            });
        }
    }

    angular.module('mailListApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('mailListController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', MailListController]);
}
