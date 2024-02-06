
@extends('layout.home')
@section('title', 'Transaction')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

@section('content')
     <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Transaksi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Transaksi</li>
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
                  Transaksi
                </h3>
                
              </div><!-- /.card-header -->
              <div class="card-body">
                
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalManagementStock">
                  Tambah Transaksi
                </button>

                <div class="row mt-4">
                        <div class="col-md-3">
                            <label> Cari Nama Barang</label>
                            <input type="text" name="search_item_name" id="search_item_name" class="form-control" placeholder="Masukkan nama barang" autofocus/>
                        </div>
                        <div class="col-md-3">
                            <label> Tanggal Awal</label>
                            <input type="text" name="start_date" id="start_date" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label> Tanggal Akhir</label>
                            <input type="text" name="end_date" id="end_date" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label> Urutan Penjualan</label>
                            <select name="sort_by" id="sort_by" class="form-control"> 
                                <option value=""> Urutkan </option>
                                <option value="desc"> Tebesar ke Terkecil </option>
                                <option value="asc"> Terkecil ke Tebesar </option>
                            </select>
                        </div>
                </div>
                

                <table class="table table-striped mt-4" id="table-transactions">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Jumlah Terjual</th>
                        <th scope="col">Tanggal Transaksi</th>
                        <th scope="col">Jenis Barang</th>
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
      <form action="#" id="frm-transaction">
        @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="id" class="form-control" />
        <div class="row">
          <div class="col-md-12">
            <label> Pilih Barang</label>
             <select class="form-control" name="item_id" id="item_id"> 
                <option value=""> - Nama Barang - </option>
             </select>
          </div>
          <div class="col-md-12">
            <label> Quantity</label>
            <input type="number" min="1" class="form-control" name="total_item_sold" id="total_item_sold" />
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
    var item_name
    var start_date
    var end_date    
    var sort_by

  
    $(document).ready(function () {

        // setup start date
        $('#start_date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        // setup end date
        $('#end_date').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        // serch item name on keyup press
        $("#search_item_name").on("keyup press", function(e){
            e.preventDefault()
            item_name = $("#search_item_name").val()
            getTransaction(item_name)
        })

        $("#start_date").on("change", function(e){
            e.preventDefault()
            start_date = $("#start_date").val()
            getTransaction(item_name, start_date)
        })

        $("#end_date").on("change", function(e){
            e.preventDefault()
            end_date = $("#end_date").val()
            getTransaction(item_name, start_date, end_date)
        })

        $("#sort_by").on("change", function(e){
            e.preventDefault()
            sort_by = $("#sort_by option:selected").val()
            getTransaction(item_name, start_date, end_date, sort_by)
        })

        // load data
        getTransaction()
        getItems()

        $("#frm-transaction").on("submit", function(e){
          e.preventDefault()
          $.ajax({
            type: "POST",
            url: "/transactions/save",
            data: {
              id : $("#id").val(),
              total_item_sold : $("#total_item_sold").val(),
              item_id : $("#item_id option:selected").val(),
            },
            dataType: "JSON",
            success: function (response) {
              if(response.status == 422){
                alert("Mohon Lengkapi transaksi form !")
              }
              if(response.status == 200){
                  window.location.href = '/transaction'
              }
            }
          });
        })

    });

    function getTransaction(item_name = null, start_date = null, end_date = null, sort_by = null){
        $.ajax({
          type: "GET",
          url: "/transactions",
          data: {
            item_name : item_name,
            start_date : start_date,
            end_date: end_date,
            sort_by : sort_by
          },
          dataType: "JSON",
          success: function (response) {
            var data = response.data
            var row = ""
            var number = 1
            $("#table-transactions").find("tr:gt(0)").remove();
            $.each(data, function (i, val) {
                row += "<tr><td>"+ (number++) +"</td> <td>"+val.items.item_name+"</td><td> "+val.items.stock+" </td><td>"+val.total_item_sold+"</td><td>"+val.transaction_date+"</td><td>"+val.items.item_type+"</td></tr>"
            });
            $("#table-transactions > tbody:last-child").append(row); 
          }
        });
    }

    function getItems(){
        $.ajax({
            type: "GEt",
            url: "/items",
            data: "data",
            dataType: "JSON",
            success: function (response) {
                //console.log(response)
                var data = response.data
                var option = ""
                $("#item_id").html()
            
                $.each(data, function (i, val) { 
                    option += "<option value="+val.id+"> "+val.item_name+" </option>"
                });
                $("#item_id").append(option)
            }
        });
    }

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

@endsection
@section('pagespecificscripts')
   
@stop