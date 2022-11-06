@include('admin.includes.header')
<body class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="semi-dark-layout">

<!-- END: Head-->
@include('admin.includes.topmenu')
<!-- BEGIN: Body-->
@include('admin.includes.sidemenu')
    <!-- BEGIN: Header-->

<div class="app-content content" style="background-color:#fff;">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">CATEGORY</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">HOME</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">CATEGORY</a>
                                    </li>
                                    <li class="breadcrumb-item active">CATEGORY VIEW
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="content-body">
            <a href = "{{ url('/admin/add_category') }}"><button type="button" class="btn btn-relief-warning mr-1 mb-1">Add</button></a>
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Success</h4>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <p class="mb-0">
                {!! session('message') !!}
                </p>
               
            </div>               
            @endif
           
            <section id="data-list-view" class="data-list-view-header">
            


                <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors dataTable" id="categories">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>NAME</th>                                    
                                    <!--th>IMAGE</th-->
                                    <th>URL</th>
                                    <th>DISCOUNT</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->category_name }}</td>
                                        <!--td><img src = "{{ asset('uploads/images/category/'.$category->category_image) }}" width="50" height="50"></td-->
                                        <td>{{ $category->category_url }}</td>
                                        <td>{{ $category->category_discount }}</td>
                                        <td>@if($category->status == 1) Active @else In-Active @endif</td>
                                        <td><a href = "{{ url('/admin/edit_category/'.$category->id) }}">
                                        <button type="button" class="btn btn-relief-success mr-1 mb-1 waves-effect waves-light"><i class="fa fa-pencil"></i></button></a>
                                        <a href = "{{ url('/admin/delete_category/'.$category->id) }}"><button type="button" class="btn btn-relief-danger mr-1 mb-1 waves-effect waves-light"><i class="fa fa-trash-o"></i></button></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            </table>
            </section>
            </div>
        </div>
</div>


    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>   
@include('admin.includes.footer')

<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

<script>

$('.dataex-html5-selectors').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [ 0, ':visible' ]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'JSON',
                action: function ( e, dt, button, config ) {
                    var data = dt.buttons.exportData();

                    $.fn.dataTable.fileSave(
                        new Blob( [ JSON.stringify( data ) ] ),
                        'Export.json'
                    );
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });

</script>