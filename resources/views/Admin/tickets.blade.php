<!-- Page Wrapper -->
<div class="page-wrapper">
    <!-- Page Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tickets</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin') }}">Paneli i admin</a>
                        </li>
                        <li class="breadcrumb-item active">Tickets</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-md-12">
                <div class="card-group m-b-30">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="d-block">Tickets tani</span>
                                </div>
                            </div>
                            <h3 class="mb-3">112</h3>
                            <div class="progress mb-2" style="height: 5px">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="d-block">Tickets të kryera</span>
                                </div>
                            </div>
                            <h3 class="mb-3">70</h3>
                            <div class="progress mb-2" style="height: 5px">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="d-block">Tickets në pritje</span>
                                </div>
                                <div></div>
                            </div>
                            <h3 class="mb-3">100</h3>
                            <div class="progress mb-2" style="height: 5px">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="row filter-row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 col-12">
                <div class="form-group form-focus select-focus">
                    <select class="select floating">
                        <option>-- Zgjedh --</option>
                        <option>Të reja</option>
                        <option>Në pritje</option>
                        <option>Të kryera</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                <a href="#" class="btn btn-success btn-block"> Kërko </a>
            </div>
        </div>
        <!-- /Search Filter -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket Subject</th>
                                <th>Puntori</th>
                                <th>Data Kërkesës</th>
                                <th>Data e Përfundimit</th>
                                <th>Statusi</th>
                                <th class="text-right">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Laptop Issue</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#">{{ $ticket->user->name }}</a>
                                    </h2>
                                </td>
                                <td>5 Jan 2019 07:21 AM</td>
                                <td>5 Jan 2019 11.12 AM</td>
                                <td>
                                    <div class="dropdown action-label">
                                        <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-dot-circle-o text-danger"></i> E re
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-success"></i> Në pritje</a>
                                            <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-danger"></i> E kryer</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_ticket"><i class="fa fa-trash-o m-r-5"></i> Fshij</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    <!-- Delete Ticket Modal -->
    <div class="modal custom-modal fade" id="delete_ticket" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Ticket</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <div class="col-6">
                                <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Ticket Modal -->
</div>
<!-- /Page Wrapper -->
