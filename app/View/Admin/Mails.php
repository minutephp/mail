<div class="content-wrapper ng-cloak" ng-app="mailListApp" ng-controller="mailListController as mainCtrl" ng-init="init()">
    <div class="admin-content">
        <section class="content-header">
            <h1><span translate="">List of mails</span> <small><span translate="">(e-mail templates)</span></small></h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li class="active"><i class="fa fa-mail"></i> <span translate="">Site E-Mails</span></li>
            </ol>
        </section>

        <section class="content">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span translate="">All mails</span>
                    </h3>

                    <div class="box-tools">
                        <a class="btn btn-sm btn-primary btn-flat" ng-href="/admin/mails/edit">
                            <i class="fa fa-plus-circle"></i> <span translate="">Create new mail</span>
                        </a>
                    </div>
                </div>

                <div class="box-body">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-bar list-group-item-bar-{{!!mail.contents.length && 'success' || 'danger'}}"
                             ng-repeat="mail in mails" ng-click-container="mainCtrl.actions(mail)">
                            <div class="pull-left">
                                <h4 class="list-group-item-heading">{{mail.name | truncate: 30: '...'}}<span class="hidden-xs">: {{mail.contents[0].subject | ucfirst}}</span></h4>
                                <p class="list-group-item-text hidden-xs">
                                    <span translate="">Description:</span> {{mail.description}}.
                                    <span translate="">Category:</span> {{mail.type | ucfirst}}.
                                </p>
                            </div>
                            <div class="md-actions pull-right">
                                <a class="btn btn-default btn-flat btn-sm" ng-href="/admin/mails/edit/{{mail.mail_id}}">
                                    <i class="fa fa-pencil-square-o"></i> <span translate="">Edit..</span>
                                </a>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-md-push-6">
                            <minute-pager class="pull-right" on="mails" no-results="{{'No mails found' | translate}}"></minute-pager>
                        </div>
                        <div class="col-xs-12 col-md-6 col-md-pull-6">
                            <minute-search-bar on="mails" columns="name, contents.subject" label="{{'Search mail..' | translate}}"></minute-search-bar>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
