{**
* Copyright (c) 2021. OrangePix  All rights reserved.
* DISCLAIMER
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@orangepix.it
*
* @author    Davide Mazzonetto <davide.mazzonetto@orangepix.it> 
*  @copyright OrangePix Srl
* @license   Do not edit, modify or copy this file
*}
 
{if $homeslider.slides}
<header id="dSlider" class="dSlider-text">
    <div id="js_dSlider" class="dSlider" data-arrows="true" data-autoplay="true">
      {foreach from=$homeslider.slides item=slide}
        {$img_url = {$slide.image_url}}
        {if !empty($slide.image_mobile_url) && Context::getContext()->isMobile()}
            {$img_url = $slide.image_mobile_url}
        {/if}
        {$isUrlValid = !empty($slide.url) && !in_array($slide.url,['#','/#','http://#','https://#'])}
        <div class="dSlider-slide row align-items-center justify-content-center">
          {if empty($slide.cta) && $isUrlValid}
            <a href="{$slide.url nofilter}" class="position-absolute w-100 h-100" style="z-index: 3" ></a>
          {/if}
          <img src="{$img_url}" alt="{$slide.title}">   
          <aside class="dOverlay"></aside>       
          <div class="dSlider-content col-12 col-xl-6">
            <div>
                {if $slide.title || $slide.description }
                    <h3 class="dSlider-subtitle text-white mb-0">{$slide.legend}</h3>
                    <h2 class="dSlider-title text-white mb-0">{$slide.title}</h2>
                    <div class="dSlider-description text-white">{$slide.description nofilter}</div>
                    {if !empty($slide.cta) && $isUrlValid}
                        <a href="{$slide.url nofilter}" class="dSlider-button btn btn-light mt-xl-2">
                            {$slide.cta}
                        </a>
                    {/if}
                {/if}
            </div>
          </div>
        </div>
      {/foreach}
  </div>
</header>
{/if} 



