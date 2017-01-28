<div class="content-wrapper ng-cloak" ng-app="mailEditApp" ng-controller="mailEditController as mainCtrl" ng-init="init()">
    <div class="admin-content" minute-hot-keys="{'ctrl+s':mainCtrl.saveAll}">
        <section class="content-header">
            <h1>
                <span translate="" ng-show="!mail.mail_id">Create new new</span>
                <span ng-show="!!mail.mail_id"><span translate="">Edit {{mail.name}}</span></span>
            </h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li><a href="" ng-href="/admin/mails"><i class="fa fa-mail"></i> <span translate="">Mails</span></a></li>
                <li class="active"><i class="fa fa-edit"></i> <span translate="">Edit mail</span></li>
            </ol>
        </section>

        <section class="content">
            <ng-switch on="(!mail.mail_id || !!data.showProps)">
                <form class="form-horizontal" name="mailForm" ng-submit="mainCtrl.saveMail()" ng-switch-when="true">
                    <div class="box box-{{mailForm.$valid && 'success' || 'danger'}}">
                        <div class="box-header with-border">
                            <h3 class="box-title"><span translate="">Mail properties</span></h3>

                            <div class="box-tools" ng-show="!!mail.mail_id">
                                <button class="btn btn-sm btn-default btn-flat" ng-click="data.showProps = false" tooltip="Back"><i class="fa fa-angle-left"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="name"><span translate="">Template name:</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Template name" ng-model="mail.name" ng-required="true">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="type"><span translate="">Type:</span></label>
                                <div class="col-sm-9">
                                    <select id="type" ng-model="mail.type" ng-required="true" class="form-control">
                                        <option ng-repeat="(key, value) in data.types" value="{{key}}">{{value}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="description"><span translate="">Description:</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="description" placeholder="Enter Description" ng-model="mail.description" ng-required="false">
                                </div>
                            </div>

                        </div>

                        <div class="box-footer with-border">
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-flat btn-primary">
                                        <span translate="" ng-show="!mail.mail_id">Create</span>
                                        <span translate="" ng-show="!!mail.mail_id">Update</span>
                                        <span translate="">mail</span>
                                        <i class="fa fa-fw fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div ng-switch-when="false">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><span translate="">Mail contents</span></h3>

                            <div class="box-tools">
                                <button class="btn btn-sm btn-default btn-flat" ng-click="mainCtrl.create()"><i class="fa fa-plus-circle"></i>
                                    <span translate="">Add </span>
                                    <span translate="" ng-show="!mail.contents.length">Content</span>
                                    <span translate="" ng-show="!!mail.contents.length" tooltip="You can add multiple contents in each mail for split testing">Variation</span>
                                    <button class="btn btn-sm btn-default btn-flat" tooltip="{{'Show mail template properties' | translate}}" ng-click="data.showProps = true">
                                        <i class="fa fa-cog"></i>
                                    </button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="tabs-panel">
                                <ul class="nav nav-tabs">
                                    <li ng-class="{active: content === data.tabs.selectedContent}" ng-repeat="content in mail.contents"
                                        ng-init="data.tabs.selectedContent = data.tabs.selectedContent || content">
                                        <a href="" ng-click="data.tabs.selectedContent = content">Content #{{$index+1}}
                                            <button class="close closeTab" type="button" ng-click="content.removeConfirm()"><i class="fa fa-times"></i></button>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" ng-repeat="content in mail.contents" ng-if="content === data.tabs.selectedContent">
                                        <form class="form" name="contentForm" ng-submit="mainCtrl.saveContent(content)">
                                            <div class="form-group">
                                                <label class="control-label pull-left" for="subject">
                                                    <span translate="">Subject:</span>
                                                </label>
                                                <div class="pull-right" ng-show="mail.contents.length > 1">
                                                    <div class="dropdown pull-right">
                                                        <button class="btn btn-transparent btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                            <li ng-repeat="copy in mail.contents" ng-show="content !== copy">
                                                                <a href="" ng-click="content.subject = copy.subject"><span translate="">Copy subject from Content #</span> {{$index + 1}}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <input type="text" class="form-control" id="subject" placeholder="Enter Subject" ng-model="content.subject" ng-required="true">
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label pull-left" for="body"><span translate="">Body:</span></label>
                                                <div class="pull-right" ng-show="mail.contents.length > 1">
                                                    <div class="dropdown pull-right">
                                                        <button class="btn btn-transparent btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                            <li ng-repeat="copy in mail.contents" ng-show="content !== copy">
                                                                <a href="" ng-click="content.html = copy.html"><span translate="">Copy body from Content #</span> {{$index + 1}}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="clearfix"></div>

                                                <ng-wig id="editor" ng-model="content.html" source-mode-allowed></ng-wig>
                                                <p class="help-block">
                                                    <a href="" ng-click="mainCtrl.showTags()"><i class="fa fa-tags"></i> <span translate="">E-mail tags are supported</span></a>
                                                </p>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label" for="body"><span translate="">Attachment:</span></label>
                                                <div>
                                                    <minute-uploader ng-model="content.attachment" type="other" preview="true" remove="true" label="Upload.."></minute-uploader>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label" for="settings"><span translate="">Content settings:</span></label>
                                                <div>
                                                    <label class="checkbox-inline"><input type="checkbox" ng-model="content.enabled"> <span translate="">Enabled</span></label>
                                                    <label class="checkbox-inline"><input type="checkbox" ng-model="content.track_opens"> <span translate="">Track opens</span></label>
                                                    <label class="checkbox-inline"><input type="checkbox" ng-model="content.track_clicks"> <span translate="">Track clicks</span></label>
                                                    <label class="checkbox-inline"><input type="checkbox" ng-model="content.embed_images"> <span translate="">Embed images</span></label>
                                                    <label class="checkbox-inline"><input type="checkbox" ng-model="content.unsubscribe_link"> <span translate="">Add unsubscribe link</span></label>
                                                </div>
                                            </div>

                                            <div class="form-group" ng-if="!!content.stats.mail_content_id">
                                                <label class="control-label" for="settings" tooltip="Stats are not real-time"><span translate="">Content statistics:</span></label>
                                                <div>
                                                    <label class="checkbox-inline"><span translate="">Sent:</span> {{content.stats.sent || 0}}.</label>
                                                    <label class="checkbox-inline"><span translate="">Opens:</span> {{content.stats.opens || 0}}.</label>
                                                    <label class="checkbox-inline"><span translate="">Clicks:</span> {{content.stats.clicks || 0}}.</label>
                                                    <label class="checkbox-inline"><span translate="">Unsubscribes:</span> {{content.stats.unsubscribes || 0}}.</label>
                                                        <label class="checkbox-inline"><span translate="">Conversions:</span> {{content.stats.conversions || 0}}.</label>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-flat btn-primary" ng-disabled="!contentForm.$valid || !content.html">
                                                    <span translate>Save content</span> <i class="fa fa-fw fa-angle-right"></i>
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ng-switch>
        </section>
    </div>

    <script type="text/ng-template" id="/tags.html">
        <div class="box">
            <div class="box-header with-border">
                <b class="pull-left"><span translate="">E-mail tags</span></b>
                <a class="pull-right close-button" href=""><i class="fa fa-times"></i></a>
            </div>

            <div class="box-body">
                <p><b translate>Tags are dynamic placeholders that are replaced in the actual email that goes out to a user.</b></p>

                <p><b>{auth}:</b> <span translate>Creates an automatic "authorization" and redirect link (for one click sign-in, valid for 24 hours).</span></p>
                <p><span translate="">Example: {auth}/members inside any email will be converted into an automatic sign-in link which redirects to "/members".</span></p>

                <hr>

                <p><b><span translate="">Public keys:</span></b>
                    <span translate>All key defined in config under the "public" key can be used as tags. So {domain}, {site_name}, etc will be replaced with domain name, site's name, etc.</span></p>
                <p><span translate="">Example: Create a new key {signature} that contains something like "Regards, John Doe" and then you can sign your email's with "{signature}" tag.</span></p>

                <hr>

                <p><b><span translate="">User keys:</span></b> <span translate>You can use tags like {first_name}, {last_name}, {email}, etc - basically all the column names from the `user` and
                        `user_data` table (except password).</span></p>
                <p><span translate="">Example: "Hi {first_name}," will be sent out as "Hi John," or "Hi Member," (if "{first_name}" is empty)</span></p>
            </div>
        </div>
    </script>

</div>
