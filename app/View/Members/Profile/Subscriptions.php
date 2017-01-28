<div class="content-wrapper ng-cloak" ng-app="subscriptionEditApp" ng-controller="subscriptionEditController as mainCtrl">
    <div class="members-content">
        <section class="content">
            <minute-event name="IMPORT_MAIL_TYPES" as="data.mail_types" on-change="mainCtrl.load(data)"></minute-event>

            <div id="tabs"></div>

            <div class="tab-content">
                <form class="form-horizontal" name="profileForm" ng-submit="mainCtrl.save()">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <span translate="">Your communication preferences</span>
                        </div>

                        <div class="box-body">
                            <div class="form-group" ng-repeat="alert in data.mail_types.types">
                                <label class="col-sm-4 control-label" for="{{alert}}">{{data.mail_types.hints[alert] || alert}}:</label>

                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio" ng-model="data.settings[alert]" name="{{alert}}" ng-value="true" ng-required="true"> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" ng-model="data.settings[alert]" name="{{alert}}" ng-value="false" ng-required="true"> No
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer with-border">
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <button type="submit" class="btn btn-flat btn-primary">
                                        <span translate="">Update my subscriptions</span>
                                        <i class="fa fa-fw fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
