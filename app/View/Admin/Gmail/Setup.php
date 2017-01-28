<div class="content-wrapper ng-cloak" ng-app="gmailConfigApp" ng-controller="gmailConfigController as mainCtrl" ng-init="init()">
    <div class="admin-content">
        <section class="content-header">
            <h1>
                <span translate="">Gmail settings</span>
            </h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li class="active"><i class="fa fa-cog"></i> <span translate="">Gmail settings</span></li>
            </ol>
        </section>

        <section class="content">
            <form class="form-horizontal" name="gmailForm" ng-submit="mainCtrl.save()">
                <div class="box box-{{gmailForm.$valid && 'success' || 'danger'}}">
                    <div class="box-header with-border">
                        <span translate="">Setup gmail integration</span>
                    </div>

                    <div class="box-body">
                        <ng-switch on="!settings.gmail.auth.token">
                            <div ng-switch-when="false">
                                <div class="alert alert-info alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <i class="fa fa-info-circle"></i> <span translate="">Gmail is now successfully integrated. If you wish to change your Gmail account, </span>
                                    <a href="" ng-click="settings.gmail.auth.token = ''"><span translate="">click here</span></a>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="reply_to"><span translate="">Reply to:</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="reply_to" placeholder="Enter Reply to" ng-model="settings.gmail.replyTo" ng-required="true">
                                        <p class="help-block" ng-show="mainCtrl.isGmail(settings.gmail.replyTo)">
                                            <a href="" google-search="gmail for business"><span translate="">How to get a non @gmail reply-to address?</span></a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div ng-switch-when="true">
                                <p><span translate="">Gmail integration enables a lot of useful features like users can reply to support tickets by email, verify their accounts by replying to email, etc.</span>
                                </p>
                                <p ng-show="!data.show"><span translate="">To setup Gmail integration, </span><a href="" ng-click="data.show = true"><span translate="">click here</span></a>.</p>
                                <div ng-show="!!data.show">
                                    <p><span translate="">Step 1: Create a New gmail account for this site, something like {{session.site.domain}}@gmail.com</span></p>
                                    <p><span translate="">Step 2: Create a New project in Google's developer console</span> <a google-search="google developer console" href=""><i
                                                class="fa fa-external-link"></i></a></p>
                                    <p><span translate="">Step 3: Click Enable API button and enable the following APIs: Gmail API and Google+ API</span></p>
                                    <p><span translate="">Step 4: Click "Go to Credentials" and create credentials for OAuth Client ID (web application)</span></p>
                                    <p><span translate="">Step 5: Select Web application and use the following Authorized Redirect URI:</span><br>
                                        <span class="fake-link">{{session.site.host}}/admin/gmail/authorize</span></p>
                                    <p><span translate="">Step 6: Copy your Client Id and Client Secret and paste it below</span></p>
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="client_id"><span translate="">Client Id:</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="client_id" placeholder="Enter Client Id" ng-model="settings.gmail.auth.id" ng-required="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label" for="client_secret"><span translate="">Client secret:</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="client_secret" placeholder="Enter Client secret" ng-model="settings.gmail.auth.secret" ng-required="true">
                                    </div>
                                </div>
                            </div>
                        </ng-switch>
                    </div>

                    <div class="box-footer with-border">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-flat btn-primary">
                                    <span translate="">Update settings</span>
                                    <i class="fa fa-fw fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
