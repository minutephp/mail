/// <reference path="../../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var App;
(function (App) {
    var SubscriptionEditController = (function () {
        function SubscriptionEditController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.load = function (data) {
                if (data && data.hasOwnProperty('types')) {
                    angular.forEach(data.types, function (v, k) {
                        _this.$scope.data.settings[v] = !_this.find(v);
                    });
                }
            };
            this.find = function (type) {
                for (var _i = 0, _a = _this.$scope.unsubscribes; _i < _a.length; _i++) {
                    var item = _a[_i];
                    if (item.attr('mail_type') == type) {
                        return item;
                    }
                }
                return false;
            };
            this.save = function () {
                var _this = this;
                var data = this.$scope.data;
                angular.forEach(data.mail_types.types, function (v, k) {
                    console.log("v: ", v);
                    var enabled = data.settings[v] === false;
                    var exists = _this.find(v);
                    if (enabled && !exists) {
                        _this.$scope.unsubscribes.create().attr('mail_type', v).save();
                    }
                    else if (!enabled && exists) {
                        exists.remove();
                    }
                });
                this.$ui.toast(this.gettext('Your communication preferences have been updated'), 'success');
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = { settings: {} };
        }
        ;
        return SubscriptionEditController;
    }());
    App.SubscriptionEditController = SubscriptionEditController;
    angular.module('subscriptionEditApp', ['MinuteFramework', 'MembersApp', 'gettext'])
        .controller('subscriptionEditController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', SubscriptionEditController]);
})(App || (App = {}));
