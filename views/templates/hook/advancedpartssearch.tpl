<!-- Block mymodule -->
<div id="mymodule_block_home" class="block col-lg-12 clearfix">
  <div class="block_content">
    <form id="searchbox" class="form-inline" method="get" action="{$base_url}search">
      <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="search_category" class="form-control" onchange="getBrands(this)">
          <option>-- Select Category --</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="brand">Brand</label>
        <select id="brand" name="search_brand" class="form-control" onchange="getSeries(this)">
          <option>-- Select Brand --</option>
        </select>
      </div>

      <div class="form-group">
        <label for="series">Series</label>
        <select id="series" name="search_series" class="form-control" onchange="getModels(this)">
          <option>-- Select Series --</option>
        </select>
      </div>

      <div class="form-group">
        <label for="model">Model</label>
        <select id="model" name="search_model" class="form-control">
          <option>-- Select Model --</option>
        </select>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-default btn-search" name="submit_search">Search</button>
      </div>
    </form>  
    
  </div>
</div>
<!-- /Block mymodule -->

<!-- Module jQuery -->
<script>

function getData(params)
{
  $.ajax({    
    type: "POST",
    url: '{$base_url}modules/advancedpartssearch/ajax.php',
    data: params,
    success: function(data){

      var opts = $.parseJSON(data);
      var id = name = null;

      for (var j = 0; j < opts.length; j++) {
        $.each(opts[j], function(i, d) {                
          if(i == 'id') id = d;
          if(i == 'name') name = d;
        });

        if(id != null && name != null) {
          $(params.node).append('<option value="' + id + '">' + name + '</option>');
          id = name = null;
        }
      }
    }
  });
}

getCategories();

function getCategories() {
  var selector = '#category';
  $(selector).empty();
  var params = { node: selector, 'method': 'getCategories' };
  getData(params);
}

function getFeatures(selector, method) {
  $(selector).empty();
  var dropDown = document.getElementById("category");
  var category = dropDown.options[dropDown.selectedIndex].value;
  var params = { node: selector, 'category': category, 'method': method };
  getData(params);
}

function getBrands(obj) {
  var selector = '#brand';
  var method = 'getBrands';
  getFeatures(selector, method);
}

function getSeries(obj) {
  var selector = '#series';
  var method = 'getSeries';
  getFeatures(selector, method);
}

function getModels(obj) {
  var selector = '#model';
  var method = 'getModels';
  getFeatures(selector, method);
}

</script>