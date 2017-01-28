/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var MailEditController = (function () {
        function MailEditController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.showTags = function () {
                _this.$ui.popupUrl('/tags.html');
            };
            this.saveContent = function (content) {
                var div = $('<div>').html(content.attr('html'));
                div.find('a').each(function () {
                    $(this).append("\n" + $(this).attr('href'));
                });
                content.attr('text', div.text()).save(_this.gettext('Content saved'));
            };
            this.create = function () {
                _this.$scope.data.tabs.selectedContent = _this.$scope.mail.contents.create().attr('track_opens', true).attr('track_clicks', true).attr('embed_images', true)
                    .attr('unsubscribe_link', true).attr('enabled', true);
            };
            this.saveMail = function () {
                _this.$scope.mail.save(_this.gettext('Mail saved successfully')).then(function () { return _this.$scope.data.showProps = false; });
            };
            this.saveAll = function () {
                angular.forEach(_this.$scope.mail.contents, _this.saveContent);
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.mail = $scope.mails[0] || this.$scope.mails.create().attr('type', 'support');
            $scope.data = {
                tabs: {},
                types: {
                    account: gettext('Account'),
                    support: gettext('Support'),
                    billing: gettext('Billing'),
                    tip: gettext('Tip'),
                    offer: gettext('Offer'),
                    announcement: gettext('Announcement'),
                    other: gettext('Other')
                }
            };
            $scope.$watch('mail.mail_id + mail.contents.length', function () {
                if ($scope.mail.mail_id) {
                    if (!$scope.mail.contents.length) {
                        _this.create();
                    }
                    $scope.data.tabs.selectedContent = $scope.mail.contents[$scope.mail.contents.length - 1];
                }
            });
        }
        return MailEditController;
    }());
    Admin.MailEditController = MailEditController;
    angular.module('mailEditApp', ['MinuteFramework', 'AdminApp', 'gettext', 'ngWig'])
        .controller('mailEditController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', MailEditController]);
})(Admin || (Admin = {}));
