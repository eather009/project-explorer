<?php
    require_once './resource/config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="./resource/img/favicon.ico" type="image/x-icon" />
        <title>Project Explorer</title>
        <link rel="stylesheet" href="./resource/css/bootstrap.min.css">
        <link rel="stylesheet" href="./resource/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="./resource/css/bootstrap-responsive.min.css">

        <link data-require="ng-table@*" rel="stylesheet" href="./resource/css/ng-table.css" />
        <link rel="stylesheet" href="./resource/css/style.css">

    </head>
    <body ng-app="myModule" ng-controller="Dirs">
        <?php
            chdir(BASE_DIR);
            $dirs = glob('*');
            $allData = array();
            if (!empty($dirs)) {

                $i = 0;
                foreach ($dirs as $index => $folder) {
                    if ($folder == '.' || $folder == basename(dirname(__FILE__)))
                        continue;
                    //$fStat = stat($folder);
                    if (is_file($folder)) {
                        $ext = explode('.', $folder);
                        $ext = array_reverse($ext);
                        $ext = $ext[0];
                    } else {
                        $ext = 'dir';
                    }
                    $portal = '';
//                    if(file_exists(trim($folder).'/admin/index.php')){
//                        $portal = "<a target='_blank' href='{$folder}/admin'><small><em>Admin</em></small></a>"; ;
//                    }

                    $cookieList = !empty($_COOKIE['cookie']) ? $_COOKIE['cookie'] : array();

                    if (!empty($cookieList)) {
                        foreach ($cookieList as $key => $value) {
                            if ($key == $folder) {
                                foreach ($value as $portalName => $portalUrl) {
                                    $portal .= (!empty($portal) ? ', ' : '') . $portalUrl;
                                }
                            }
                        }
                    }


                    $allData[] = array('index' => $i++, 'portal' => $portal, 'ftype' => $ext, 'url' => trim($folder));
                }
            }
            $jsonData = json_encode($allData);
        ?>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">Project Explorer &COPY; Eather</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href='./' >Project List</a></li>
                        <li ><a href='/phpmyadmin' target="_blank">Php MyAdmin</a></li>
                    </ul>
                    <form class="navbar-form navbar-left search-form-top" role="search">
                        <label for="SearchForm" class="control-label">Filter:</label>
                        <div class="form-group">
                            <input type="text" ng-model="filter.url" placeholder="Search by Project Name" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />
                        </div>
                        <div class="form-group">
                            <input type="text" ng-model="filter.ftype" placeholder="Search by File Type" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />
                        </div>
                        <div class="form-group">
                            <input type="text" ng-model="filter.portal" placeholder="Search by Portal" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />  
                        </div>
                    </form>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        <div class="container-fluid">
            <div class="panel panel-info search-form-content">
                <div class="panel-heading">
                    Filter:
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group col-md-4 col-sm-4">
                            <label for="SearchbyProjectName" class="col-md-6 col-sm-6 control-label">Project Name</label>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" ng-model="filter.url" placeholder="Search by Project Name" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-sm-4">
                            <label for="SearchbyFileType" class="col-md-5 col-sm-6 control-label">File Type</label>
                            <div class="col-md-7 col-sm-6">
                                <input type="text" ng-model="filter.ftype" placeholder="Search by File Type" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-sm-4">
                            <label for="SearchbyFilePortal" class="col-md-5 col-sm-6 control-label">Portal</label>
                            <div class="col-md-7 col-sm-6">
                                <input type="text" ng-model="filter.portal" placeholder="Search by Portal" class="input-filter form-control ng-pristine ng-valid ng-scope ng-touched" />
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

            <table ng-table="tableParams" class="table table-striped">
                <tr ng-repeat="folder in $data" >
                    <td data-title="'SI.'" sortable="'index'" style="text-align:center;width: 5%; ">{{$index + ((tableParams.page() - 1) * tableParams.count()) + 1}}</td>
                    <td sortable="'url'" data-title="'Project Name'" class='ftype ' style="background: url(resource/img/{{folder.ftype}}.png) left center no-repeat; width: 65%; "  ><a target="__blank" href='http://<?php echo BASE_URL . '/'?>{{folder.url}}' >{{folder.url}}</a></td> 
                    <td data-title="'Type'" style="text-align:center;width: 10%; ">{{folder.ftype}}</td>
                    <td data-title="'Portals'"  style="text-align:center;width: 20%; ">
                        <div ng-bind-html="folder.portal" style="display: inline;"></div>
                        <a ng-click="launch(folder.index)" class="btn btn-info btn-xs">Portal</a>
                    </td>
                </tr>
            </table>

        </div>

        <script src="./resource/js/jquery.min.js"></script>
        <script src="./resource/js/angular.min.js"></script>
        <script src="./resource/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="./resource/js/ui-bootstrap-tpls-0.6.0.js" type="text/javascript"></script>
        <script src="./resource/js/dialogs.min.js" type="text/javascript"></script>
        <script src="./resource/js/ng-sanitize.js"></script>
        <script src="./resource/js/ng-table.js"></script>
        <script src="./resource/js/angular-cookies.js"></script>



        <script>
                                        var app = angular.module('myModule', ['ngTable', 'ui.bootstrap', 'dialogs', 'ngCookies','ngSanitize']).controller('Dirs', function ($scope, $filter, ngTableParams, $rootScope, $timeout, $dialogs) {
                                            var data = <?php echo $jsonData; ?>;
                                            $scope.portal = '';
                                            $scope.launch = function (id) {
                                                var dlg = null;
                                                var url = (data[id].url) ? data[id].url : '';

                                                dlg = $dialogs.create('/dialogs/whatsyourportal.html', 'whatsYourPortalCtrl', {url: url}, {key: false, back: 'static'});
                                                dlg.result.then(function (value) {

                                                var date = new Date();
                                                date.setTime(date.getTime() + (90 * 24 * 60 * 60 * 1000));
                                                var expires = "; expires=" + date.toGMTString();
                                                
                                                value.portal = value.portal.toUpperCase();
                                                if(value.portal_url_full!='')
                                                    document.cookie = "cookie[" + data[id].url + "][" + value.portal + "]=" + "<a target='_blank' href='http://" + value.portal_url_full + "'><small><em>" + value.portal + "</em></small></a>" + expires + "; path=/";

                                                var allcookies = document.cookie;
                                                cookiearray = allcookies.split(';');
                                                var urls = '';
                                                for (var i in cookiearray)
                                                {
                                                    var cookieName = cookiearray[i].split('=');

                                                    var matched = cookieName[0].indexOf("cookie[" + data[id].url + "]");
                                                    if (matched >= 0 && cookieName!='') {
                                                        urls += ((urls != '') ? ', ' : '') + cookiearray[i].replace(cookieName[0] + "=", "");
                                                    } else {
                                                    }

                                                }

                                                data[id].portal = urls;

                                                }, function () {
                                                    $scope.portal = '';
                                                });

                                            }; // end launch

                                            // for faking the progress bar in the wait dialog
                                            var progress = 25;
                                            var msgs = [
                                                'Hey! I\'m waiting here...',
                                                'About half way done...',
                                                'Almost there?',
                                                'Woo Hoo! I made it!'
                                            ];
                                            var i = 0;

                                            var fakeProgress = function () {
                                                $timeout(function () {
                                                    if (progress < 100) {
                                                        progress += 25;
                                                        $rootScope.$broadcast('dialogs.wait.progress', {msg: msgs[i++], 'progress': progress});
                                                        fakeProgress();
                                                    } else {
                                                        $rootScope.$broadcast('dialogs.wait.complete');
                                                    }
                                                }, 1000);
                                            }; // end fakeProgress 

                                            $scope.filter = {ftype: 'dir'};
                                            $scope.tableParams = new ngTableParams({
                                                page: 1, // show first page
                                                count: 15, // count per page
                                                sorting: {
                                                    index: 'asc'     // initial sorting
                                                },
                                                filter: $scope.filter
                                            }, {
                                                total: data.length, // length of data
                                                getData: function ($defer, params) {
                                                    // use build-in angular filter
                                                    var filteredData = params.filter() ?
                                                        $filter('filter')(data, params.filter()) :
                                                        data;

                                                    var orderedData = params.sorting() ?
                                                        $filter('orderBy')(filteredData, params.orderBy()) :
                                                        data;

                                                    if (params.filter())
                                                    {
                                                        if (params.$params.page > Math.ceil(orderedData.length / params.$params.count))
                                                            params.$params.page = 1;
                                                    }
                                                    params.total(orderedData.length); // set total for recalc pagination
                                                    $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
//                                                    $defer.resolve(orderedData);
                                                }
                                            });

                                        }).controller('whatsYourPortalCtrl',function ($scope, $modalInstance,$cookies, data) {
                                            $scope.user = {portal: '', portal_url: '', dataurl: data.url, portal_url_full: ('<?php echo BASE_URL; ?>' + '/' + data.url + '/')};
                                            
                                            $scope.cancel = function () {
                                                $modalInstance.dismiss('canceled');
                                            }; // end cancel

                                            $scope.save = function () {
                                                $modalInstance.close($scope.user);
                                            }; // end save

                                            $scope.remove = function () {
                                                $scope.user.portal = $scope.user.portal.toUpperCase();
                                                if($scope.user.portal != ''){
                                                    var allcookies = $cookies;
                                                    cookiearray = Object.keys(allcookies);
                                                    for (var i in cookiearray)
                                                    {
                                                        if (cookiearray[i].indexOf("cookie[" + $scope.user.dataurl + "]["+ $scope.user.portal + "]")>=0) {
                                                            cookieStr = cookiearray[i] + "=" + escape('') + "; ";
                                                            var today = new Date();
                                                            var expr = new Date(today.getTime() + -1 * 24 * 60 * 60 * 1000);
                                                            cookieStr += "expires=" + expr.toGMTString() + "; ";
                                                            cookieStr += "path=" + '/' + "; ";
                                                            document.cookie = cookieStr;
                                                        } 

                                                    }

                                                    $scope.user.portal_url_full = ''; 

                                                    $modalInstance.close($scope.user);
                                                }else{
                                                    
                                                }
                                            }; // end save
                                            $scope.hitDelete = function (evt) {
                                                console.log($scope);
                                                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.portal, null)))
                                                    $scope.remove();

                                            };
                                            $scope.hitEnter = function (evt) {
                                                if ($scope.user.portal_url.length > 0)
                                                    $scope.user.portal_url_full = '<?php echo BASE_URL; ?>' + '/' + data.url + '/' + $scope.user.portal_url;
                                                else
                                                    $scope.user.portal_url_full = '<?php echo BASE_URL; ?>' + '/' + data.url + '/';

                                                if (angular.equals(evt.keyCode, 13) && !(angular.equals($scope.portal_url_full, null) || angular.equals($scope.portal_url_full, '')))
                                                    $scope.save();

                                            }; // end hitEnter

                                        }).run(['$templateCache', function ($templateCache) {
                                                $templateCache.put('/dialogs/whatsyourportal.html', '<div class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"><span class="glyphicon glyphicon-sunglasses"></span> Portal </h4></div><div class="modal-body"><div class="alert alert-warning">Duplicate Portal will be replaced!</div><ng-form name="portalDialog" novalidate role="form"><div class="form-group input-group-lg" ng-class="{true: \'has-error\'}[portalDialog.portal.$dirty && portalDialog.portal.$invalid]"><label class="control-label" for="portal">Portal Name:</label><input type="text" class="form-control" name="portal" id="portal" ng-model="user.portal" required><span class="help-block label label-warning">Click on Delete to delete this portal.</span></div><div class="form-group input-group-lg" ng-class="{true: \'has-error\'}[portalDialog.portal_url.$dirty && portalDialog.portal_url.$invalid]"><label class="control-label" for="portal_url">Portal Url:</label><input type="text" class="form-control" name="portal_url" id="portal_url" ng-model="user.portal_url" ng-keyup="hitEnter($event)" ></div><div class="form-group input-group-lg" ng-class="{true: \'has-error\'}[portalDialog.portal_url_full.$dirty && portalDialog.portal_url_full.$invalid]"><label class="control-label" for="portal_url_full">Full URL:</label><input type="text" class="form-control" name="portal_url_full" id="portal_url_full" ng-model="user.portal_url_full" keyup="hitEnter($event)" required><span class="help-block">You can modify URL.</span></div></ng-form></div><div class="modal-footer"><button type="button" class="btn btn-default" ng-click="cancel()">Cancel</button><button type="button" class="btn btn-primary" ng-click="save()" ng-disabled="(portalDialog.$dirty && portalDialog.$invalid) || portalDialog.$pristine">Save</button><button type="button" class="btn btn-danger" ng-disabled="(portalDialog.portal.$invalid) || portalDialog.$pristine" ng-click="remove()" >Delete</button></div></div></div></div>');
                                            }]); // end run / module;
        </script>
    </body>
</html>