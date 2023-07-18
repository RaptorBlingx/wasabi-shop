
# PrestaShop Orange Theme

Demo : [https://orangetheme.orangepix.it/](https://orangetheme.orangepix.it/)

Main features :
- form accessibility and better validation
- SEO: Hn, rel prev/next for pagination...
- font performance
- better responsive
  
### New smarty blocks
- layoutWrapperClass
- contentWrapperClass
- pageHeaderClass
- pageContentClass
- pageFooterClass

### New image sizes
We use srcset in product-cover-thumbnails.tpl for responsive images.

    pdt_180:
      width: 180
      height: 180
      scope: [products]
    pdt_300:
      width: 300
      height: 300
      scope: [products]
    pdt_360:
      width: 360
      height: 360
      scope: [products]
    pdt_540:
      width: 540
      height: 540
      scope: [products]

## SEO
- Better pagination with link rel next/prev (in templates/_partials/pagination-seo.tpl)
- name="robots" content="none" for ordered listing page
- Open Graph and JSON-LD structured data
- font load from Google (in templates/_partials/font.tpl)

## Compatibility
PrestaShop 1.8.x