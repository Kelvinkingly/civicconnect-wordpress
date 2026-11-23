<?php
if (!defined('ABSPATH')) exit;
add_action('after_setup_theme',function(){add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('automatic-feed-links');});
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('civicconnect',get_stylesheet_uri(),array(),'1.0.0');wp_enqueue_script('civicconnect',get_template_directory_uri().'/app.js',array(),'1.0.0',true);});
function cc_entries(){return array(
 'cc_service'=>array(
 array('Building & planning','Permits, development applications and planning enquiries.','Planning','building'),
 array('Waste & recycling','Explore collection information, recycling guidelines and waste service options.','Environment','leaf'),
 array('Roads & infrastructure','Find information about transportation, street maintenance and public works.','Public works','road'),
 array('Parks & recreation','Discover community parks, leisure facilities and recreation programmes.','Community','tree'),
 array('Business & licensing','Explore business registration and local licensing information.','Business','briefcase'),
 array('Property & taxation','Find property assessment, rates and payment information.','Finance','wallet')),
 'cc_department'=>array(
 array('Administration & Council','Council governance, meeting information and community engagement.','Governance','building'),
 array('Community & Neighbourhoods','Community programming, inclusion and citizen engagement.','Community','people'),
 array('Public Works & Environment','Infrastructure, transport, waste and environmental services.','Infrastructure','leaf'),
 array('Planning & Economic Development','Land use, permits, business development and investment services.','Development','briefcase'),
 array('Finance & Revenue','Budget information, revenue services and public financial records.','Finance','wallet'),
 array('Customer & Digital Services','Digital access, citizen support and service request navigation.','Support','chat')),
 'cc_news'=>array(
 array('Community improvement priorities','Explore an illustrative infrastructure improvement and neighbourhood engagement programme.','Community update','road'),
 array('A more connected community','Explore the proposed digital service experience and opportunities for citizen participation.','Service update','people'),
 array('Planning for a greener future','Discover an illustrative municipal sustainability and waste reduction initiative.','Environment','leaf')),
 'cc_document'=>array(
 array('Council meeting agenda','Sample agenda covering declarations, public participation, reports and resolutions.','Agenda','document'),
 array('Community development framework','Sample planning framework for neighbourhood improvement and inclusive access.','Planning','document'),
 array('Annual budget overview','Illustrative budget summary structure, with no actual municipal financial figures.','Finance','document'),
 array('Public participation guidelines','Example guidance for community consultations and submitting feedback.','Policy','document')),
 'cc_event'=>array(
 array('Community listening session','Explore a sample community dialogue and citizen participation event.','Community','calendar'),
 array('Council public meeting','Explore a sample council meeting and public agenda experience.','Governance','calendar'),
 array('Neighbourhood clean-up','Discover a sample volunteer and environmental activity.','Environment','calendar'))
);}
add_action('init',function(){foreach(array('cc_service'=>'Services','cc_department'=>'Departments','cc_news'=>'News','cc_document'=>'Public documents','cc_event'=>'Events') as $type=>$title){register_post_type($type,array('label'=>$title,'public'=>true,'show_in_rest'=>true,'has_archive'=>true,'supports'=>array('title','editor','excerpt','thumbnail'),'menu_icon'=>'dashicons-admin-site-alt3'));}register_taxonomy('cc_category',array_keys(cc_entries()),array('label'=>'Categories','public'=>true,'show_in_rest'=>true));});
add_action('after_switch_theme',function(){if(get_option('cc_seeded_v1'))return;foreach(cc_entries() as $type=>$entries){foreach($entries as $entry){$id=wp_insert_post(array('post_type'=>$type,'post_title'=>$entry[0],'post_content'=>$entry[1],'post_status'=>'publish'),true);if(!is_wp_error($id)){wp_set_object_terms($id,$entry[2],'cc_category');update_post_meta($id,'cc_icon',$entry[3]);}}}update_option('cc_seeded_v1',1);flush_rewrite_rules();});
function cc_icon($name){$paths=array('building'=>'<path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6M9 10h.01M15 10h.01"/>','leaf'=>'<path d="M20 4C9 4 4 8 4 15a5 5 0 0 0 5 5c7 0 11-7 11-16ZM4 21l10-10"/>','road'=>'<path d="M6 21 10 3M18 21 14 3M12 7v3m0 4v3"/>','tree'=>'<path d="M12 22v-6M6 16h12l-3-5h3L12 2 6 11h3Z"/>','briefcase'=>'<rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V4h8v3M3 12h18"/>','wallet'=>'<rect x="3" y="5" width="18" height="15" rx="2"/><path d="M16 11h5m-3 3h.01"/>','people'=>'<circle cx="9" cy="7" r="3"/><circle cx="17" cy="8" r="2"/><path d="M3 21v-4a6 6 0 0 1 12 0v4m2-8a4 4 0 0 1 4 4v4"/>','chat'=>'<path d="M3 4h18v13H9l-6 4Zm4 5h10m-10 4h7"/>','document'=>'<path d="M5 3h10l4 4v14H5Zm9 0v5h5M8 12h8m-8 4h8"/>','calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18m-13 4h2m4 0h2m-8 4h2"/>','search'=>'<circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/>');return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($paths[$name]??$paths['document']).'</svg>';}
function cc_cards($type){$q=new WP_Query(array('post_type'=>$type,'posts_per_page'=>-1,'orderby'=>'date','order'=>'ASC'));while($q->have_posts()){$q->the_post();$name=get_the_title();$body=wp_strip_all_tags(get_the_content());$cats=wp_get_post_terms(get_the_ID(),'cc_category',array('fields'=>'names'));$cat=is_wp_error($cats)?'':implode(' · ',$cats);$icon=get_post_meta(get_the_ID(),'cc_icon',true)?:'document';$news=$type==='cc_news';?><article class="card searchable <?php echo $news?'news-card':''; ?>" data-search="<?php echo esc_attr($name.' '.$body.' '.$cat); ?>"><?php if($news):?><div class="image-band"><?php echo cc_icon($icon); ?></div><div class="card-body"><?php else:?><div class="icon"><?php echo cc_icon($icon); ?></div><?php endif; ?><span class="tag"><?php echo esc_html($cat); ?></span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html($body); ?></p><a class="more" href="<?php the_permalink(); ?>">View details</a><?php if($news):?></div><?php endif; ?></article><?php }wp_reset_postdata();}
