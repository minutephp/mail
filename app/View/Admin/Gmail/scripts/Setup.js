/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var GmailConfigController = (function () {
        function GmailConfigController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.isGmail = function (str) {
                return (str || '').toLowerCase().indexOf('@gmail.com') !== -1;
            };
            this.save = function () {
                _this.$scope.config.save(_this.gettext('Gmail saved successfully'));
                if (!_this.$scope.settings.gmail.auth.token) {
                    _this.$ui.confirm(_this.gettext('In next step, login to the Gmail account you have setup for this site and authorize Gmail access')).then(function () { return top.location.href = '/admin/gmail/start'; });
                }
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = { processors: [], tabs: {} };
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'google').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.gmail = $scope.settings.gmail || { auth: {} };
        }
        return GmailConfigController;
    }());
    Admin.GmailConfigController = GmailConfigController;
    angular.module('gmailConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('gmailConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', GmailConfigController]);
})(Admin || (Admin = {}));
