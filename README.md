# a3w-cf7-fields-attributes
A WordPress plugin addon for Contact Form 7 plugin to add custom attributes in cf7 fields shortcodes

## Why use it?
This plugin was originaly created in order to add `title` attribute in fields for accessibility reason on forms that haven't `<label></label>` element because of visual aspect.  
WebAIM recommands usage of `title` or `aria-label` attribute when label is invisible or out of the form.  
[ @see WebAIM recommandations  ](https://webaim.org/techniques/forms/advanced#invisible)

## Installation
- Download the last release of the plugin (.zip)
- Go to WordPress admin > Plugins > Add
- Click on Upload
- Upload the plugin .zip
- Activate the plugin

## Usage
- Edit a CF7 form
- Click on "Custom attribute generator" in tag list area
- Fill the fields "attribute's name" and "attribut's content"
- Click on the button "Copy and close"
- Paste the generated attribute inside the field shortcode related to
- That's it!

#### Example :
> attribute's name = title  
> attribute's content = Your Custom Title  
> will generates a custom attribute like this:  
> `attr_title=Your%20Custom%20Title`  
> copy/paste it in cf7 field shortcode:  
>  `[text custom_title attr_title=Your%20Custom%20Title placeholder "Your Custom Title" ]`  
> and on front end it will generate:  
> `<input type="text" placeholder="Your Custom Title" name="custom_title" title="Your Custom Title" />`  

## Limitations
By default the plugin allows you to use only following attributes:
- title
- aria-{name}
- data-{name}

You can use this filter `a3w_cf7_fields_attributes_allowed_names` to allow other attributes names.

> ** IMPORTANT NOTICE **
> ** Keep in mind that this limitation was added to prevent duplicate attributes in html (id, name, class...) **

Example of usage:
```
add_filter( 'a3w_cf7_fields_attributes_allowed_names', function( $is_allowed, $attr_name ) {
    if ( $attr_name === 'my-attr-name' ) {
      return true;
    }
    return $is_allowed;
} );
```
