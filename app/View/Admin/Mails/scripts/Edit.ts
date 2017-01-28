/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class MailEditController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

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

            $scope.$watch('mail.mail_id + mail.contents.length', ()=> {
                if ($scope.mail.mail_id) {
                    if (!$scope.mail.contents.length) {
                        this.create();
                    }

                    $scope.data.tabs.selectedContent = $scope.mail.contents[$scope.mail.contents.length - 1];
                }
            });
        }

        showTags = () => {
            this.$ui.popupUrl('/tags.html');
        };

        saveContent = (content) => {
            var div = $('<div>').html(content.attr('html'));
            div.find('a').each(function () {
                $(this).append("\n" + $(this).attr('href'));
            });

            content.attr('text', div.text()).save(this.gettext('Content saved'));
        };

        create = () => {
            this.$scope.data.tabs.selectedContent = this.$scope.mail.contents.create().attr('track_opens', true).attr('track_clicks', true).attr('embed_images', true)
                .attr('unsubscribe_link', true).attr('enabled', true);
        };

        saveMail = () => {
            this.$scope.mail.save(this.gettext('Mail saved successfully')).then(() => this.$scope.data.showProps = false);
        };

        saveAll = () => {
            angular.forEach(this.$scope.mail.contents, this.saveContent);
        };
    }

    angular.module('mailEditApp', ['MinuteFramework', 'AdminApp', 'gettext', 'ngWig'])
        .controller('mailEditController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', MailEditController]);
}
