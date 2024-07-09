<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<style>
    .paypal-button-tagline{
        color:white !important;
    }
</style>
<?php 
$total = 0;
    $qry = $conn->query("SELECT c.*,p.title,i.price,p.id as pid from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where c.client_id = ".$_settings->userdata('id'));
    while($row= $qry->fetch_assoc()):
        $total += $row['price'] * $row['quantity'];
    endwhile;
?>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body"></div>
            <h3 class="text-center"><b>Checkout</b></h3>
            <hr class="border-dark">
            <form action="" id="place_order">
                <input type="hidden" name="amount" value="<?php echo $total ?>">
                <input type="hidden" name="payment_method" value="cod">
                <input type="hidden" name="paid" value="0">
                <div class="row row-col-1 justify-content-center">
                    <div class="col-6">
                    <div class="form-group col mb-0">
                    <label for="" class="control-label">Order Type</label>
                    </div>
                    <div class="form-group d-flex pl-4">
                        <div class="custom-control custom-radio">
                          <input class="custom-control-input custom-control-input-primary" type="radio" id="customRadio4" name="order_type" value="2" checked="">
                          <label for="customRadio4" class="custom-control-label">For Delivery</label>
                        </div>
                        <div class="custom-control custom-radio ml-3">
                          <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="1">
                          <label for="customRadio5" class="custom-control-label">transper bank</label>
                        </div>
                        
                        
                      </div>
                      
                        <div class="form-group col address-holder">
                            <label for="" class="control-label">Delivery Address</label>
                            <textarea id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                        </div>
                        <div class="col">
                            <span><h4><b>Total:</b> <?php echo number_format($total) ?></h4></span>
                        </div>
                        <hr>
                        <div class="col my-3">
                        <h4 class="text-muted">Payment Method</h4>
                            <div class="d-flex w-100 justify-content-between">
                                <button class="btn btn-flat btn-primary">Cash on Delivery</button>
                                <span id="paypal-button" class="text-light"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card">
                        <div class="card-header alert alert-primary">
                        Informasi Pembayaran
                        </div>
                        <div class="card-body">
                        <p class="text-success mb-3 card-header alert alert-primary">Silahkan melakukan pembayaran melalui nomor rekening di bawah ini :</p>

                        <ul class="list-group list-group-flush card-header alert alert-primary">
                            <li class="header alert alert-primary">Bank Mandiri 1212423344</li>
                            <li class="header alert alert-primary">Bank BCA 645623534</li>
                            <li class="header alert alert-primary">Bank BNI 56435645</li>
                        </ul>

                        <?php
                        if(empty($tr->bukti_pembayaran)){ ?>
                            <!-- Button trigger modal -->
                            <button style="width: 100%;" type="button" class="btn btn-sm btn-danger mt-3" data-toggle="modal" data-target="#exampleModal">
                            Upload Bukti Pembayaran
                            </button>
                        <?php }
                        elseif($tr->status_pembayaran == '0'){ ?>
                            <button style="width: 100%;" class="btn btn-sm btn-warning mt-3"><i class="fa fa-clock-o"></i> Menunggu Konfirmasi</button>
                        <?php }
                        elseif($tr->status_pembayaran == '1'){ ?>
                            <button style="width: 100%;" class="btn btn-sm btn-success mt-3"><i class="fa fa-check"></i> Pembayaran Selesai</button>
                        <?php } ?>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload bukti pembayaran anda</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                        <div class="form-group">
                            <label for="">Upload Bukti Pembayaran</label>
                            <input type="hidden" name="id_rental" value="">
                            <input type="file" name="bukti_pembayaran" class="form-control">
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-success">Kirim</button>
                        <img src="images" style="height: 70px">
                        </div>
                    </form>
                    </div>
                </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
paypal.Button.render({
    env: 'sandbox', // change for production if app is live,
 
        //app's client id's
	client: {
        sandbox:    'AdDNu0ZwC3bqzdjiiQlmQ4BRJsOarwyMVD_L4YQPrQm4ASuBg4bV5ZoH-uveg8K_l9JLCmipuiKt4fxn',
        //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
    },
 
    commit: true, // Show a 'Pay Now' button
 
    style: {
    	color: 'blue',
    	size: 'small'
    },
 
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                    	//total purchase
                        amount: { 
                        	total: '<?php echo $total; ?>', 
                        	currency: 'PHP' 
                        }
                    }
                ]
            }
        });
    },
 
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
    		// //sweetalert for successful transaction
    		// swal('Thank you!', 'Paypal purchase successful.', 'success');
            payment_online()
        });
    },
 
}, '#paypal-button');

function payment_online(){
    $('[name="payment_method"]').val("Online Payment")
    $('[name="paid"]').val(1)
    $('#place_order').submit()
}
$(function(){
    $('[name="order_type"]').change(function(){
        if($(this).val() ==1){
            $('.address-holder').hide('slow')
        }else{
            $('.address-holder').show('slow')
        }
    })
    $('#place_order').submit(function(e){
        e.preventDefault()
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=place_order',
            method:'POST',
            data:$(this).serialize(),
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("an error occured","error")
                end_loader();
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    alert_toast("Order Successfully placed.","success")
                    setTimeout(function(){
                        location.replace('./')
                    },2000)
                }else{
                    console.log(resp)
                    alert_toast("an error occured","error")
                    end_loader();
                }
            }
        })
    })
})
</script>