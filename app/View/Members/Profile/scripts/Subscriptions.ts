/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module App {
    export class SubscriptionEditController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = {settings: {}};
        };

        load = (data) => {
            if (data && data.hasOwnProperty('types')) {
                angular.forEach(data.types, (v, k) => {
                    this.$scope.data.settings[v] = !this.find(v);
                });
            }
        };

        find = (type) => {
            for (let item of this.$scope.unsubscribes) {
                if (item.attr('mail_type') == type) {
                    return item;
                }
            }

            return false;
        };

        save = function () {
            let data = this.$scope.data;

            angular.forEach(data.mail_types.types, (v, k) => {
                console.log("v: ", v);
                var enabled = data.settings[v] === false;
                var exists = this.find(v);

                if (enabled && !exists) {
                    this.$scope.unsubscribes.create().attr('mail_type', v).save();
                } else if (!enabled && exists) {
                    exists.remove();
                }
            });

            this.$ui.toast(this.gettext('Your communication preferences have been updated'), 'success');
        };
    }

    angular.module('subscriptionEditApp', ['MinuteFramework', 'MembersApp', 'gettext'])
        .controller('subscriptionEditController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', SubscriptionEditController]);
}
