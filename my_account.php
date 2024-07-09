    
<style>
    a.order-item{
        text-decoration:unset;
    }
    .order-item:hover{
        opacity: .5;
    }

</style>
<section class="py-2">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body">
                <div class="w-100 justify-content-between d-flex">
                    <h4><b>Orders</b></h4>
                    <a href="./?p=edit_account" class="btn btn btn-dark btn-flat"><div class="fa fa-user-cog"></div> Manage Account</a>
                </div>
                    <hr class="border-warning">
                    <div class="w-100 d-flex justify-content-center">
                    <div class="input-group mb-3 col-md-6">
                        <input type="text" class="form-control" aria-label="" aria-describedby="inputGroup-sizing-default" id="search" placeholder="Search...">
                        <span class="input-group-text" id="inputGroup-sizing-default"><i class="fa fa-search"></i></span>
                    </div>
                    </div>
                    <div class="row row-cols-sm-1 row-cols-md-4">
                        <?php 
                            $i = 1;
                            $qry = $conn->query("SELECT o.*,concat(c.firstname,' ',c.lastname) as client from `orders` o inner join clients c on c.id = o.client_id where o.client_id = '".$_settings->userdata('id')."' order by unix_timestamp(o.date_created) desc ");
                            while($row = $qry->fetch_assoc()):
                        ?>
                        <a class="callout card-dark border-0 col m-2 view_order text-light order-item" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
                            <h5><?php echo md5($row['id']) ?></h5>
                            <p class="m-0"><b>Amount:</b> <?php echo number_format($row['amount']) ?></p>
                            <div class="w-100 d-flex justify-content-between">
                                <span class='text-light'><small><em><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></em></small></span>
                                        <?php if($row['status'] == 0): ?>
                                        <span class="badge badge-sm badge-light text-dark">Pending</span>
                                        <?php elseif($row['status'] == 1): ?>
                                            <span class="badge badge-sm badge-primary">Packed</span>
                                        <?php elseif($row['status'] == 2): ?>
                                            <span class="badge badge-sm badge-warning">Out for Delivery</span>
                                        <?php elseif($row['status'] == 3): ?>
                                            <span class="badge badge-sm badge-success">Delivered</span>
                                        <?php else: ?>
                                            <span class="badge badge-sm badge-danger">Cancelled</span>
                                        <?php endif; ?>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    </div>
                    <h4 class="text-center noData" style="display:none" >No Data.</h4>
            </div>
        </div>
    </div>
</section>
<script>
    function cancel_book($id){
        start_loader()
        $.ajax({
            url:_base_url_+"classes/Master.php?f=update_book_status",
            method:"POST",
            data:{id:$id,status:2},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("an error occured",'error')
                end_loader()
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("Book cancelled successfully",'success')
                    setTimeout(function(){
                        location.reload()
                    },2000)
                }else{
                    console.log(resp)
                    alert_toast("an error occured",'error')
                }
                end_loader()
            }
        })
    }
    function check_item(){
        if($('.order-item:visible').length <= 0){
            $('.noData').show('slow')
        }else{
            $('.noData').hide('slow')
        }
    }
    $(function(){
        check_item()
        $('.view_order').click(function(){
            uni_modal("Order Details","./admin/orders/view_order.php?view=user&id="+$(this).attr('data-id'),'large')
        })
        $('#search').on('input', function(){
            var _f = $(this).val().toLowerCase();
            $('.order-item').each(function(){
                var _t = $(this).text().toLowerCase();
                if(_t.includes(_f) == true){
                    $(this).toggle(true)
                }else{
                    $(this).toggle(false)
                }
            })
            check_item()
        })

    })
</script>