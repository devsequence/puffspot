<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'crb_attach_theme_options');
function crb_attach_theme_options()
{
    $screens_labels = array(
        'plural_name' => 'sections',
        'singular_name' => 'section',
    );

    $labels = array(
        'plural_name' => 'items',
        'singular_name' => 'item',
    );
    $labels_modals = array(
        'plural_name' => 'modals',
        'singular_name' => 'modal',
    );

    Container::make('theme_options', "Site Settings")
        ->add_tab('Logo', array(
            Field::make('image', 'crb_logo', 'Logo')
                ->set_value_type('url')
                ->set_width(33),
            Field::make('text', 'crb_logo_text', 'Logo (text)')
                ->set_width(33),
        ))
        ->add_tab('Copyright', array(
            Field::make('text', 'crb_copyright', 'Copyright'),
        ));
}

add_action('carbon_fields_register_fields', 'crb_attach_in_front_page');
function crb_attach_in_front_page()
{
    $screens_labels = array(
        'plural_name' => 'sections',
        'singular_name' => 'section',
    );

    $labels = array(
        'plural_name' => 'items',
        'singular_name' => 'item',
    );
    $labels_columns = array(
        'plural_name' => 'columns',
        'singular_name' => 'column',
    );
    $labels_tabs = array(
        'plural_name' => 'tabs',
        'singular_name' => 'tab',
    );

    Container::make('post_meta', 'Sections on the Home Page')
        ->show_on_template('home.php')
        ->add_fields(array(
            Field::make('complex', 'screens', 'Sections')
                ->set_layout('tabbed-vertical')
                ->setup_labels($screens_labels)
                ->add_fields('screen_1', 'Section 1 - Banner', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('A word in Latin without spaces or numbers')
                        ->set_required(true),
                    Field::make('image', 'image', 'Image')->set_value_type('url'),
                    Field::make('complex', 'slider', 'List')
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('text', 'title', 'Title')->set_required(true),
                        ))
                ))
                ->add_fields('screen_2', 'Section 2 - Advantages', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('A word in Latin without spaces or numbers')
                        ->set_required(true),
                    Field::make('image', 'title_top_img', 'Logo Header')->set_value_type('url'),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Subtitle')->set_required(true),
                    Field::make('complex', 'product', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'title_img', 'Logo Header')->set_value_type('url'),
                            Field::make('text', 'title', 'Title')->set_required(true),
                            Field::make('image', 'image', 'Image')->set_value_type('url'),
                            Field::make('rich_text', 'text', 'Text')->set_required(true),
                        ))
                ))
                ->add_fields('screen_3', 'Section 3 - Our Integrations', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Subtitle')->set_required(true),
                    Field::make('complex', 'list_1', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'title_img', 'Logo')->set_value_type('url')->set_width(33),
                            Field::make('text', 'title', 'Title')->set_required(true)->set_width(33),
                            Field::make('text', 'link', 'Link')->set_required(true)->set_width(33),
                        )),
                    Field::make('complex', 'list_2', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'title_img', 'Logo')->set_value_type('url')->set_width(33),
                            Field::make('text', 'title', 'Title')->set_required(true)->set_width(33),
                            Field::make('text', 'link', 'Link')->set_required(true)->set_width(33),
                        )),
                    Field::make('complex', 'list_3', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'title_img', 'Logo')->set_value_type('url')->set_width(33),
                            Field::make('text', 'title', 'Title')->set_required(true)->set_width(33),
                            Field::make('text', 'link', 'Link')->set_required(true)->set_width(33),
                        ))
                ))
                ->add_fields('screen_4', 'Section 4 - Why Choose Us?', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Text')->set_required(true),
                    Field::make('complex', 'list', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'img', 'Image')->set_value_type('url'),
                            Field::make('text', 'title', 'Title')->set_required(true),
                            Field::make('text', 'text', 'Text')->set_required(true),
                        ))
                ))
                ->add_fields('screen_5', 'Section 5 - Price', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'form', 'Form')->set_required(true),
                    Field::make('image', 'image', 'Image')->set_value_type('url'),
                ))
                ->add_fields('screen_6', 'Section 6 - Documentation', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('image', 'image', 'Image')->set_value_type('url'),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'text', 'Text')->set_required(true),
                    Field::make('text', 'btn_link', 'Link')->set_required(true)->set_width(50),
                    Field::make('text', 'btn_text', 'Link Text')->set_required(true)->set_width(50),
                ))
                ->add_fields('screen_7', 'Section 7 - Companies', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('complex', 'list', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'img', 'Image')->set_value_type('url')->set_width(33),
                            Field::make('text', 'title', 'Title')->set_required(true)->set_width(33),
                            Field::make('text', 'link', 'Link')->set_required(true)->set_width(33),
                        ))
                ))
                ->add_fields('screen_8', 'Section 8 - Testimonials', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Subtitle')->set_required(true),
                    Field::make('complex', 'list', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('text', 'text', 'Text')->set_required(true),
                            Field::make('image', 'img', 'Image')->set_value_type('url'),
                            Field::make('text', 'title', 'Author')->set_required(true),
                            Field::make('text', 'type', 'Position')->set_required(true),
                        ))
                ))
                ->add_fields('screen_9', 'Section 9 - Team', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Subtitle')->set_required(true),
                    Field::make('complex', 'list', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('image', 'img', 'Image')->set_value_type('url'),
                            Field::make('text', 'title', 'Author')->set_required(true),
                            Field::make('text', 'type', 'Position')->set_required(true),
                        ))
                ))
                ->add_fields('screen_10', 'Section 10 - Contacts', array(
                    Field::make("separator", "crb_style_screen_off", "Disable Section?"),
                    Field::make('checkbox', 'screen_off', 'Disable Section?'),
                    Field::make("separator", "crb_style_inform", "Information"),
                    Field::make("text", "id", "Section ID (unique value)")
                        ->set_help_text('Word in Latin characters without spaces or digits')
                        ->set_required(true),
                    Field::make('text', 'title', 'Title')->set_required(true),
                    Field::make('text', 'subtitle', 'Subtitle')->set_required(true),
                    Field::make('text', 'form', 'Form')->set_required(true),
                    Field::make('complex', 'list', 'List')->set_required(true)
                        ->setup_labels($labels)
                        ->add_fields(array(
                            Field::make('text', 'title', 'Title')->set_required(true),
                            Field::make('text', 'text', 'Details')->set_required(true),
                        ))
                ))

                
        ));

        
}

add_action('after_setup_theme', 'crb_load');
function crb_load()
{
    get_template_part('vendor/autoload');
    \Carbon_Fields\Carbon_Fields::boot();
}

add_filter('crb_media_buttons_html', function ($html, $field_name) {
    if (
        $field_name === 'text' ||
        $field_name === 'subtitle' ||
        $field_name === 'crb_pp_title' ||
        $field_name === 'thanks_title' ||
        $field_name === 'modal1_title' ||
        $field_name === 'description' ||
        $field_name === 'title1' ||
        $field_name === 'table' ||
        $field_name === 'text_after' ||
        $field_name === 'text_before' ||
        $field_name === 'description_in_front_page' ||
        $field_name === 'title'
    ) {
        return;
    }
    return $html;
}, 10, 2);