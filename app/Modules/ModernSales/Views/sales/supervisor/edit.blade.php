<div class="col-sm-12 col-md-12">
    <div class="row clearfix">
        <div class="col-sm-12 col-md-6">
            <label for="supervisorType">SUPERVISOR TYPE :*</label>
            <div class="form-group">
                <div class="form-line"> 
                    <select class="form-control show-tick" name="supervisor_type" required="" onchange="getSupervisor2(this.value)" data-live-search='true'>
                        <option value="">Select Supervisor Type</option>
                        <?php foreach($user_type as $usertype){ ?>
                        <option <?php if($data->supervisor_type == $usertype->user_type_id){ echo "selected";} ?> value="<?php echo $usertype->user_type_id; ?>"><?php echo $usertype->user_type; ?></option>
                        <?php } ?>
                    </select> 
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <label for="supervisor">SUPERVISOR NAME :*</label>
            <div class="form-group">
                <div class="form-line" id="supervisor2">
                    <select class="form-control show-tick" name="supervisor_id" required="" data-live-search='true'> 
                        <option value="">Select Name</option>
                        <?php foreach($supervisor as $supervi){ ?>
                        <option <?php if($data->supervisor_id == $supervi->id){ echo "selected";} ?> value="<?php echo $supervi->id; ?>"><?php echo $supervi->display_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>                                            
    </div>
    <div class="row clearfix"> 
        <div class="col-sm-12 col-md-6">
            <label for="management_id">Management :*</label>
            <div class="form-group">
                <div class="form-line" style="height: auto;">
                    <select class="form-control show-tick" name="management_id" id="management_id" required="">
                        <option value="">Select Management</option>
                        <?php foreach($management as $managemen){ ?>
                        <option <?php if($data->management_id == $managemen->id){ echo "selected";} ?> value="<?php echo $managemen->id ?>"><?php echo $managemen->display_name ?></option>
                        <?php } ?>
                    </select>
                
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <label for="manager_id">Manager :*</label>
            <div class="form-group">
                <div class="form-line"> 
                    <select class="form-control show-tick" name="manager_id" required="" id="manager_id" >
                        <option value="">Select Manager</option>
                        <?php foreach($managers as $manage){ ?>
                        <option <?php if($data->manager_id == $manage->id){ echo "selected";} ?> value="<?php echo $manage->id; ?>">
                            <?php echo $manage->display_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-sm-12 col-md-6">
            <label for="executive_id">Executive :*</label>
            <div class="form-group">
                <div class="form-line" id="executive_id" style="height: auto;">
                    <select class="form-control show-tick" required="" name="executive_id">
                        <option value="">Select Executive</option>
                        <?php foreach($executive as $execu){?>
                        <option <?php if($data->executive_id == $execu->id){ echo "selected";} ?> value="<?php echo $execu->id; ?>"><?php echo $execu->display_name; ?></option>
                        <?php } ?>
                    </select>
                
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <label for="officer_id">Office :*</label>
            <div class="form-group">
                <div class="form-line" id="officer_id" style="height: auto;">
                    <select class="form-control show-tick" required="" name="officer_id">
                        <option value="">Select Office</option>
                        <?php foreach($officer as $offic){ ?>
                        <option <?php if($data->officer_id == $offic->id){ echo "selected";} ?> value="<?php echo $offic->id ?>"><?php echo $offic->display_name ?></option>
                        <?php } ?>
                    </select>                        
                </div>
            </div>
        </div>                                            
    </div>                                   
</div> 
<script type="text/javascript">
function getSupervisor2(slID)
{ 
    $.ajax({
        method: "GET",
        url: '<?php echo url('/mst-get_supervisor_list')?>',
        data: {id: slID}
    })
    .done(function (response){  
        $('#supervisor2').html('');   
        $('#supervisor2').append(response);   
    });            
}  
</script>