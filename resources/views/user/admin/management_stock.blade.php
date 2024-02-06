
@extends('layout.home')
@section('title', 'Management Stock')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

@section('content')
     <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Management Stock</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Management Stock</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-12">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  Management Stock
                </h3>
                
              </div><!-- /.card-header -->
              <div class="card-body">
                
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalManagementStock">
                  Tambah
                </button>

                <table class="table table-striped mt-4" id="table-items">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Jenis Barang</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->

           
          </section>
          <!-- /.Left col -->

        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

<!-- Modal -->
<div class="modal fade" id="modalManagementStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="#" id="frm-items">
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="form-control" />
        <div class="row">
          <div class="col-md-12">
            <label> Nama Barang</label>
            <input type="text" class="form-control" name="item_name" id="item_name" />
          </div>
          <div class="col-md-12">
            <label> Stock</label>
            <input type="number" min="1" class="form-control" name="stock" id="stock" />
          </div>
          <div class="col-md-12">
            <label> Jenis Barang </label>
            <select name="item_type" class="form-control" id="item_type"> 
                <option value=""> Pilih Jenis Barang </option>
                <option value="Konsumsi"> Konsumsi </option>
                <option value="Pembersih"> Pembersih </option>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>

      </form>
    </div>
  </div>
</div>

<script>
  var brand
  var model
  var is_available

    $(document).ready(function () {

        getItems()

        // submit form
        $("#frm-items").on("submit", function(e){
          e.preventDefault()
          $.ajax({
            type: "POST",
            url: "/items/create",
            data: {
              id : $("#id").val(),
              item_name: $("#item_name").val(),
              stock: $("#stock").val(),
              item_type: $("#item_type option:selected").val(),
            },
            dataType: "JSON",
            success: function (response) {
              if(response.status == 422){
                alert("Mohon Lengkapi form !")
              }
              if(response.status == 200){
                  window.location.href = '/management-stock'
              }
            }
          });
        })
    });

    function getItems(item_name = null, item_type = null){
        $.ajax({
          type: "GET",
          url: "/items",
          data: {
            item_name : item_name,
            item_type : item_type,
          },
          dataType: "JSON",
          success: function (response) {
            var data = response.data
            var row = ""
            var number = 1
            $("#table-items").find("tr:gt(0)").remove();
            $.each(data, function (i, val) {
                row += "<tr><td>"+ (number++) +"</td> <td>"+val.item_name+"</td><td> "+val.stock+" </td><td>"+val.item_type+"</td><td><button type='button' class='btn btn-sm btn-info' onclick='detailItem("+val.id+")'> Ubah </button> <button class='btn btn-sm btn-danger' onclick='deleteItem("+val.id+")'> Hapus </td></tr>"
            });
            $("#table-items > tbody:last-child").append(row); 
          }
        });
    }

    function detailItem(id = null){
      $.ajax({
        type: "GET",
        url: "/items",
        data: {
          id : id
        },
        dataType: "JSON",
        success: function (response) {
            var data = response.data[0]
            $("#id").val(data.id)
            $("#item_name").val(data.item_name)
            $("#stock").val(data.stock)
            $("#item_type option[value='"+data.item_type+"']").prop('selected', true)

            $("#modalManagementStock").modal('show');
        }
      });
    }

    function deleteItem(id = null){
      $.confirm({
          title: 'Pesan!',
          content: 'Apa anda yakin menghapus data ini ?',
          buttons: {
              confirm: function () {
                 $.ajax({
                   type: "POST",
                   url: "/items/delete",
                   data: {
                     id : id
                   },
                   dataType: "JSON",
                   success: function (response) {
                      if(response.status == 200){
                        $.confirm("Data berhasil dihapus.")
                      }
                      window.location.href = '/management-stock'
                   }
                 });
              },
              cancel: function () {
              },
          }
      });
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

@endsection
@section('pagespecificscripts')
   
@stop