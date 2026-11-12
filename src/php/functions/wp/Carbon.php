<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Carbon
{

    /**
     * @var array|string[]
     */
    private array $labels;
    /**
     * @var array|string[]
     */
    private array $labels_modals;
    /**
     * @var array|string[]
     */
    private array $screens_labels;
    /**
     * @var array|string[]
     */
    private array $fields_without_button;

    public function __construct()
    {
        $this->labels = array(
            'plural_name' => 'items',
            'singular_name' => 'item',
        );
        $this->labels_modals = array(
            'plural_name' => 'modal window',
            'singular_name' => 'modal windows',
        );
        $this->screens_labels = array(
            'plural_name' => 'screens',
            'singular_name' => 'screen',
        );
        $this->fields_without_button = array('service_description', 'text', 'subtitle', 'title', 'form_title');
        $this->add_actions();
        $this->add_filter();
    }

    private function add_actions(): void
    {
        add_action('after_setup_theme', [$this, 'crb_load']);
        add_action('carbon_fields_register_fields', [$this, 'crb_attach_theme_options']);
        add_action('carbon_fields_register_fields', [$this, 'crb_attach_in_front_page']);
        add_action('carbon_fields_register_fields', [$this, 'crb_attach_in_product_page']);
//        add_action('carbon_fields_register_fields', [$this, 'crb_attach_in_project_page']);
//        add_action('carbon_fields_register_fields', [$this, 'crb_attach_in_service_page']);
        add_action( 'carbon_fields_register_fields', [ $this, 'crb_attach_in_front_options_page' ] );
    }

    private function add_filter(): void
    {
        add_filter('crb_media_buttons_html', [$this, 'crb_media_buttons_html_filter'], 10, 2);
    }

    public function crb_media_buttons_html_filter($html, $field_name)
    {
        $fields = $this->fields_without_button;
        if (in_array($field_name, $fields)) {
            return;
        }

        return $html;
    }
    public function crb_attach_in_service_category()
    {
        Container::make('term_meta', 'Настройки категории')
            ->where('term_taxonomy', '=', 'service-category')
            ->add_fields([
                Field::make('image', 'service_category_icon', 'Иконка категории')
                    ->set_required(true)->set_value_type('url')
            ]);
    }
    public function crb_attach_in_project_page()
    {
        Container::make('post_meta', 'Настройки Проекта')
            ->where('post_type', '=', 'project')
            ->add_fields(array($this->get_crb_project_screens()));
    }
    public function get_crb_project_screens($id = 'project_screens')
    {
        return Field::make('complex', $id, 'Screens_project')
            ->set_layout('tabbed-vertical')
            ->setup_labels($this->screens_labels)
            ->add_fields('screen_1', 'Основная информация', array(
                Field::make("separator", "crb_style_screen_off", "Отключить секцию?"),
                Field::make('checkbox', 'screen_off', 'Отключить секцию?'),
                Field::make("separator", "crb_style_inform", "Информация"),
                Field::make("complex", "list", "Информация")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "Заголовок")->set_required(true)->set_width(33),
                        Field::make("image", "image", "Иконка")->set_value_type('url')->set_required(true)->set_width(33),
                    )),

            ))
            ->add_fields('screen_2', 'Галерея', array(
                Field::make("separator", "crb_style_screen_off", "Отключить секцию?"),
                Field::make('checkbox', 'screen_off', 'Отключить секцию?'),
                Field::make("separator", "crb_style_inform", "Информация"),
                Field::make("media_gallery", "media_gallery", "Галерея"),
            ))
            ->add_fields('screen_3', 'Контент', array(
                Field::make("separator", "crb_style_screen_off", "Отключить секцию?"),
                Field::make('checkbox', 'screen_off', 'Отключить секцию?'),
                Field::make("separator", "crb_style_inform", "Информация"),
                Field::make("rich_text", "rich_text", "Контент"),
            ))
            ->add_fields('screen_4', 'Баннер', array(
                Field::make("separator", "crb_style_screen_off", "Отключить секцию?"),
                Field::make('checkbox', 'screen_off', 'Отключить секцию?'),
                Field::make("separator", "crb_style_inform", "Информация"),
                Field::make("rich_text", "rich_text", "Контент"),
            ));
   }
    public function crb_attach_in_product_page() {
        Container::make( 'post_meta', 'product_options', 'Product options' )
            ->where( 'post_type', '=', 'product' )
            ->add_fields( array(
//                Field::make( 'text', 'model', 'Model' )->set_width( 50 ),
                Field::make( 'text', 'price', 'Price (number, e.g. 10.90)' )->set_width( 25 ),
                Field::make( 'text', 'sku', 'sku' )->set_width( 25 ),
//                Field::make( 'text', 'puffs', 'Puffs (number)' )->set_width( 25 ),
//                Field::make( 'text', 'liquid_volume', 'Liquid volume (ml, e.g. 28)' )->set_width( 25 ),
//                Field::make( 'text', 'battery_capacity', 'Battery capacity (mAh)' )->set_width( 25 ),
                $this->get_crb_product_screens(),
            ) );
    }
    public function get_crb_product_screens( $id = 'product_screens' ) {
        return Field::make( 'complex', $id, 'Product screens' )
            ->set_layout( 'tabbed-vertical' )
            ->setup_labels( $this->screens_labels )
            ->add_fields( 'screen_1', 'Main Info', array(
                Field::make( 'separator', 'crb_style_screen_off', 'screen off?' ),
                Field::make( 'checkbox',  'screen_off', 'screen off?' ),

                Field::make( 'separator', 'crb_style_inform', 'inform' ),
                $this->get_field_id(),

                // Заголовок можна взяти з post_title, але якщо треба окремий – робимо поле:
                Field::make( 'text', 'custom_title', 'Custom title' )->set_width( 50 ),

                // Ціна
                Field::make( 'text', 'price', 'Price (e.g. $10.90)' )->set_width( 50 ),

                // Основний опис
                Field::make( 'rich_text', 'description', 'Description' ),

                // FLAVORS
                Field::make( 'association', 'flavors_products', 'Flavors (related products)' )
                    ->set_types( array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'product',
                        ),
                    ) ),

            ) )
            ->add_fields( 'screen_2', 'best price', array(
                Field::make( 'separator', 'crb_style_adv', 'best price' ),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "title_text", "top text")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "text_1", "Text")->set_width(50),
                        Field::make("text", "text_2", "Text")->set_width(50),
                    )),
            ) );
    }
    public function crb_attach_theme_options()
    {
        Container::make('theme_options', "Site information")
            ->add_tab('Header', array(
                Field::make("image", "logo", "logo")->set_required(true)->set_width(100),
                Field::make("image", "whatsapp_logo", "whatsapp logo")->set_required(true)->set_width(50),
                Field::make("text", "whatsapp_link", "whatsapp link")->set_required(true)->set_width(50),
                Field::make("image", "telegram_logo", "telegram logo")->set_required(true)->set_width(50),
                Field::make("text", "telegram_link", "telegram link")->set_required(true)->set_width(50),

            ));
    }
    public function crb_attach_in_front_options_page()
    {
        if ( ! function_exists('pll_languages_list') || ! function_exists('pll_get_post') ) {
            return;
        }

        $default_front_page_id = (int) get_option('page_on_front');
        $front_page_ids = [$default_front_page_id];

        foreach ( pll_languages_list() as $lang ) {
            $translated_id = pll_get_post( $default_front_page_id, $lang );
            if ( $translated_id && ! in_array( (int) $translated_id, $front_page_ids, true ) ) {
                $front_page_ids[] = (int) $translated_id;
            }
        }

        Container::make('post_meta', 'page_options', 'Additional information')
            ->where('post_id', 'IN', $front_page_ids)
            ->add_tab('Additional information', array(
                Field::make("complex", "header_info", "list top line")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "text", "Текст")->set_width(50),
                    )),
                Field::make("text", "copy_text", "copy text")->set_required(true),
                Field::make("text", "popup_title", "popup title")->set_required(true),
                Field::make("text", "popup_form", "popup form")->set_required(true),
                Field::make("text", "form_bottom_text", "form bottom text")->set_required(true),

            ));
    }
    public function crb_attach_in_front_page()
    {
        Container::make('post_meta', 'Screens')
            ->where('post_template', '=', 'index.php')
            ->add_fields(array($this->get_crb_screens()));
    }

    public function get_field_id()
    {
        return Field::make("text", "id", "Section ID (unique value)")
            ->set_attribute('pattern', '^[a-z0-9\-]+$')
            ->set_help_text('A Latin word without spaces. Possible character: "-" <br> <strong>The ID value must not be repeated!</strong>')
            ->set_required(true);
    }

    public function get_crb_screens($id = 'screens')
    {
        return Field::make('complex', $id, 'Screens')
            ->set_layout('tabbed-vertical')
            ->setup_labels($this->screens_labels)
            ->add_fields('screen_1', 'Hero', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("complex", "list", "slide")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "title")->set_required(true),
                        Field::make("rich_text", "text", "text")->set_required(true),
                        Field::make("text", "btn_link_text", "text link")->set_required(true)->set_width(50),
                        Field::make("text", "btn_link", "link")->set_required(true)->set_width(50),
                        Field::make("image", "img", "image")->set_value_type('url')->set_required(true),
                    )),
                Field::make("complex", "list_2", "hero banner")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "title"),
                        Field::make("text", "subtitle", "subtitle"),
                        Field::make("text", "text", "text"),
                        Field::make("image", "img", "image")->set_value_type('url')->set_required(true),
                    )),
            ))
            ->add_fields('screen_2', 'advantages', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(33),
                        Field::make("text", "icon_text", "text")->set_required(true)->set_width(33),
                    )),
            ))
            ->add_fields('screen_3', 'products', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "Title")
                    ->set_required(true)
                    ->set_width(50),
                Field::make('select', 'products_mode', 'Products mode')
                    ->set_width(50)
                    ->set_options(array(
                        'catalog'   => 'Catalog (all products)',
                        'newest'    => 'New arrivals (newest)',
                        'hits'      => 'Sales hits',
                        'manual'    => 'Manual (selected products)',
                    ))
                    ->set_default_value('catalog'),

                Field::make('association', 'crb_association', 'Selected products')
                    ->set_types(array(
                        array(
                            'type'      => 'post',
                            'post_type' => 'product',
                        )
                    ))
                    ->set_conditional_logic( array(
                        array(
                            'field'   => 'products_mode',
                            'value'   => 'manual',
                            'compare' => '=',
                        )
                    )),
            ))
            ->add_fields('screen_4', 'banner', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "bg", "bg")->set_required(true)->set_width(33),
                        Field::make("image", "image", "image")->set_required(true)->set_width(33),
                        Field::make("text", "class", "text")->set_required(true)->set_width(33),
                        Field::make("text", "title", "title")->set_required(true)->set_width(50),
                        Field::make("text", "text", "text")->set_required(true)->set_width(50),
                    )),
            ))
            ->add_fields('screen_5', 'reviews', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "reviews_number", "reviews number")->set_width(50),
                Field::make("text", "reviews_info", "reviews info")->set_width(50),
                Field::make("text", "reviews_trustpilot", "reviews trustpilot")->set_width(50),
                Field::make("complex", "list_trustpilot", "list trustpilot")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "image", "image")->set_required(true)->set_width(33),
                    )),
                Field::make("text", "reviews_number_star", "reviews number star")->set_width(50),
                Field::make("text", "reviews_info_trusted_by", "Trusted by")->set_width(50),
                Field::make("text", "trusted_text_link", "trusted text link")->set_width(50),
                Field::make("text", "trusted_link", "trusted link")->set_width(50),
                Field::make("complex", "reviews_list", "reviews list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "image", "image")->set_required(true)->set_width(33),
                        Field::make("text", "name", "name")->set_required(true)->set_width(50),
                        Field::make("text", "date", "date")->set_required(true)->set_width(50),
                        Field::make("text", "text", "text")->set_required(true)->set_width(50),
                        Field::make("text", "reviews_number", "reviews number")->set_required(true)->set_width(50),
                    )),
            ))
            ->add_fields('screen_6', 'our advantages', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(33),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(50),
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                    )),

            ))
            ->add_fields('screen_7', 'distributor', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "title_text", "top text")->set_width(50),
                Field::make("image", "bg", "bg image")->set_required(true)->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                    )),
            ))
            ->add_fields('screen_8', 'FAQ', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                    )),
            ))
            ->add_fields('screen_9', 'Contact', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "text", "text")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(25),
                        Field::make("text", "title", "title")->set_width(25),
                        Field::make("text", "link", "link")->set_width(25),
                        Field::make("text", "link_text", "link text")->set_width(25),
                    )),
                Field::make("text", "form_shortcode", "form shortcode"),
            ))
            ->add_fields('screen_10', 'Seo text', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "title", "title"),
                        Field::make("rich_text", "text", "text"),
                    )),
            ))
            ->add_fields('screen_11', 'hero banner', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("image", "image", "image")->set_required(true)->set_width(33),
                Field::make("text", "title", "title")->set_required(true)->set_width(33),
                Field::make("text", "text", "text")->set_required(true)->set_width(33),
            ))
            ->add_fields('screen_12', 'Payment methods', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "title_text", "top text")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(25),
                        Field::make("text", "title", "title")->set_width(25),
                        Field::make("text", "link", "link")->set_width(25),
                    )),
            ))
            ->add_fields('screen_13', 'our advantages banner', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(33),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(50),
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                    )),
                Field::make("image", "bg_banner", "bg")->set_required(true)->set_width(33),
                Field::make("image", "image_banner", "image")->set_required(true)->set_width(33),
                Field::make("text", "title_banner", "title")->set_required(true)->set_width(50),
                Field::make("text", "text_banner", "text")->set_required(true)->set_width(50),
            ))
            ->add_fields('screen_14', 'brands', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("media_gallery", "media_gallery", "media gallery"),
            ))
            ->add_fields('screen_15', 'about', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(33),
                Field::make("rich_text", "text", "title")->set_required(true)->set_width(50),
                Field::make("image", "image", "image")->set_required(true)->set_width(50),
                Field::make("text", "link", "link text")->set_width(25),

            ))
            ->add_fields('screen_16', 'competitors', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("text", "title_text", "top text")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(50),
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                    )),
            ))
            ->add_fields('screen_17', 'Original products', array(
                Field::make("separator", "crb_style_screen_off", "screen off?"),
                Field::make('checkbox', 'screen_off', 'screen off?'),
                Field::make("separator", "crb_style_inform", "inform"),
                $this->get_field_id(),
                Field::make("text", "title", "title")->set_width(50),
                Field::make("complex", "list", "list")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("image", "icon", "icon")->set_required(true)->set_width(50),
                        Field::make("text", "title", "title")->set_width(50),
                        Field::make("text", "text", "text")->set_width(100),
                        Field::make("media_gallery", "media_gallery", "media gallery"),
                        Field::make("text", "original_link", "original link"),
                        Field::make("text", "original_link_text", "original link text"),

                    )),
            ))
            ->add_fields('screen_2222', 'Бегущая сторка', array(
                Field::make("separator", "crb_style_screen_off", "Отключить секцию?"),
                Field::make('checkbox', 'screen_off', 'Отключить секцию?'),
                Field::make("separator", "crb_style_inform", "Информация"),
                $this->get_field_id(),
                Field::make("complex", "list", "Список")
                    ->setup_labels($this->labels)
                    ->add_fields(array(
                        Field::make("text", "text", "Текст")->set_required(true)->set_width(33),
                    )),
            ))
        ;
    }
    public function crb_load(): void
    {
        if (!class_exists('Carbon_Fields\Carbon_Fields')) {
            get_template_part('vendor/autoload');
            \Carbon_Fields\Carbon_Fields::boot();
        }
    }
}

new Carbon();