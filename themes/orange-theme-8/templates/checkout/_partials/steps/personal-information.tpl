{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
    {hook h='displayPersonalInformationTop' customer=$customer}

    {if $customer.is_logged && !$customer.is_guest}

    <p class="identity">
      {* [1][/1] is for a HTML tag. *}
      {l s='Connected as [1]%firstname% %lastname%[/1].'
        d='Shop.Theme.Customeraccount'
        sprintf=[
          '[1]' => "<a href='{$urls.pages.identity}'>",
          '[/1]' => "</a>",
          '%firstname%' => $customer.firstname,
          '%lastname%' => $customer.lastname
        ]
      }
    </p>
    <p>
      {* [1][/1] is for a HTML tag. *}
      {l
        s='Not you? [1]Log out[/1]'
        d='Shop.Theme.Customeraccount'
        sprintf=[
        '[1]' => "<a href='{$urls.actions.logout}'>",
        '[/1]' => "</a>"
        ]
      }
    </p>
    {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
      <p><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
    {/if}

    <div class="clearfix">
      <form method="GET" action="{$urls.pages.order}">
        <button
                class="continue btn btn-primary btn-lg"
                name="controller"
                type="submit"
                value="order"
        >
            {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>

    </div>

  {else}
    <ul class="nav nav-tabs nav-tabs--center my-2 nav-tabs-info border-0 row w-100 flex-wrap checkout-tabs mx-auto" role="tablist">
      
      <li class="nav-item col-12 col-lg-6">
        <small class="d-block text-center mb-2 text-dark">{l s='If you are already registered, access from here.' d='Shop.OrangethemeCheckout'}</small>
        <a
          class="btn btn-primary my-1 w-100"
          data-toggle="tab"
          href="#checkout-login-form"
          role="tab"
          aria-controls="checkout-login-form"
          aria-selected="false"
        >          
          {l s='I\'m already registered' d='Shop.OrangethemeCheckout'}
        </a>
      </li>

      <li class="nav-item col-12 col-lg-6 mt-4 mt-lg-0">
        <small class="d-block text-center mb-2 text-dark">{l s='Register on Wasabi, create an account or shop as a guest.' d='Shop.OrangethemeCheckout'}</small>
        <a
          class="btn  btn-primary my-1 w-100"
          data-toggle="tab"
          href="#checkout-guest-form"
          role="tab"
          aria-controls="checkout-guest-form"
          aria-selected="false"
          >
          {if $guest_allowed}
            {l s='I am a new user' d='Shop.OrangethemeCheckout'}
          {else}
            {l s='I am a new user' d='Shop.OrangethemeCheckout'}
          {/if}
        </a>
      </li>
      
    </ul>
    <div class="tab-content">
      <div class="checkout-form tab-pane" id="checkout-login-form" role="tabpanel" {if !$show_login_form}aria-hidden="true"{/if}>
        <div class="row">
          <div class="col-12">
            {render file='checkout/_partials/login-form.tpl' ui=$login_form}    
          </div>
        </div>      
      </div>        
      <div class="checkout-form tab-pane" id="checkout-guest-form" role="tabpanel" {if $show_login_form}aria-hidden="true"{/if}>
        <div class="row">
          <div class="col-12">
            {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}    
          </div>
        </div>      
      </div>
    </div>
    <script>
      if (registerHasErrors || loginHasErrors) {
        const [loginForm, registerFrom] = [
          document.getElementById('checkout-login-form'),
          document.getElementById('checkout-guest-form')
        ]
        if (loginHasErrors) loginForm?.classList.add('active')
        else registerFrom?.classList.add('active')
        
      }
    </script>

  {/if}
{/block}
