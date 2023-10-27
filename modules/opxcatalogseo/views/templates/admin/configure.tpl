<div class="panel toolbarBox">
  <div class="btn-toolbar">
    <form method="post" id="ocsForm">
      <ul class="nav nav-pills">
        <button id="ocsUpAll" name="ocsUpAll" type="submit" class="btn btn-danger">{l s='Update all' mod='opxcatalogseo'}</button>
        <button id="ocsFill" name="ocsFill" type="submit" class="btn btn-success">{l s='Fill missings' mod='opxcatalogseo'}</button>
        &nbsp; &nbsp; &nbsp; 
        <div id="ocsChkGroup">
          <label for="ocsChkAll">{l s='All' mod="opxcatalogseo"}</label>
          <input id="ocsChkAll" name="ocsChkAll" type="checkbox" checked>
          <label>
            &nbsp; &nbsp; &nbsp; 
            {l s='Products' mod='opxcatalogseo'}
            <input id="ocsChkProduct" name="ocsChkProduct" type="checkbox" checked>
            </label>
            <label>
            &nbsp; &nbsp; &nbsp; 
            {l s='Categories' mod='opxcatalogseo'}
            <input id="ocsChkCategory" name="ocsChkCategory" type="checkbox" checked>
            &nbsp; &nbsp; &nbsp; 
            {l s='Manufacturers' mod='opxcatalogseo'}
            <input id="ocsChkManufacturer" name="ocsChkManufacturer" type="checkbox" checked>
          </label>
        </div>
      </ul>
    </form>
  </div>
</div>

<style>
#ocsChkAll:checked ~ label {
  opacity: .2;
  pointer-events: none;
}
#ocsChkGroup {
  display: inline;
}
</style>

<script>
ocsChkAll.oninput = () => {
  if (ocsChkAll.checked) {
    ocsChkGroup.querySelectorAll('input').forEach(input => input.checked = true);
  }
}

ocsForm.onsubmit =  e => {
  e.preventDefault()
}

ocsUpAll.onclick =  e => {
  e.preventDefault()
  {capture name="ocsTrans" assign="ocsAreYouSure"}
    {l s='Are you sure to update all?' mod='opxcatalogseo'}
  {/capture}
  if (confirm({$ocsAreYouSure|trim|json_encode})) {
    const fd = new FormData(ocsForm, ocsUpAll)
    ocsAjaxRequest(fd)
  }
}

ocsFill.onclick =  e => {
  e.preventDefault()
  {capture name="ocsTrans" assign="ocsAreYouSure"}
    {l s='Are you sure to fill all missings?' mod='opxcatalogseo'}
  {/capture}
  if (confirm({$ocsAreYouSure|trim|json_encode})) {
    const fd = new FormData(ocsForm, ocsFill)
    ocsAjaxRequest(fd)
  }
}

function ocsAjaxRequest(formData) {
  fetch(location.href + '&ajax=1', {
    method: 'post',
    body: formData
  })
  .then(r => r.ok && r.json())
  .then(r => {
    if (r.status) {
      return location.href += "&success"
    }
    {capture name="ocsTrans" assign="ocsError"}
      {l s='Something went wrong. Check the console for further information' mod='opxcatalogseo'}
    {/capture}
    if (r.message) {
      alert({$ocsError|trim|json_encode})
      console.group('Error')
      console.log(r.message)
      console.groupEnd()
    }
  })
}
</script>