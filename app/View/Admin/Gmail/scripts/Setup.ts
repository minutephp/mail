/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class GmailConfigController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = {processors: [], tabs: {}};
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'google').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.gmail = $scope.settings.gmail || {auth: {}};
        }

        isGmail = (str) => {
            return (str || '').toLowerCase().indexOf('@gmail.com') !== -1;
        };

        save = () => {
            this.$scope.config.save(this.gettext('Gmail saved successfully'));

            if (!this.$scope.settings.gmail.auth.token) {
                this.$ui.confirm(this.gettext('In next step, login to the Gmail account you have setup for this site and authorize Gmail access')).then(
                    () => top.location.href = '/admin/gmail/start'
                );
            }
        };
    }

    angular.module('gmailConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('gmailConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', GmailConfigController]);
}
