<?php
/** no direct access **/
defined('MECEXEC') or die();

$ix_options = $this->main->get_ix_options();
?>
<div class="wrap" id="mec-wrap">
    <h1><?php esc_html_e('MEC Import / Export', 'mec'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_url($this->main->remove_qs_var('tab')); ?>" class="nav-tab"><?php echo esc_html__('Google Cal. Import', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-g-calendar-export')); ?>" class="nav-tab"><?php echo esc_html__('Google Cal. Export', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-f-calendar-import')); ?>" class="nav-tab"><?php echo esc_html__('Facebook Cal. Import', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-meetup-import')); ?>" class="nav-tab nav-tab-active"><?php echo esc_html__('Meetup Import', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-sync')); ?>" class="nav-tab"><?php echo esc_html__('Synchronization', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-export')); ?>" class="nav-tab"><?php echo esc_html__('Export', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-import')); ?>" class="nav-tab"><?php echo esc_html__('Import', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-thirdparty')); ?>" class="nav-tab"><?php echo esc_html__('Third Party Plugins', 'mec'); ?></a>
        <a href="<?php echo esc_url($this->main->add_qs_var('tab', 'MEC-test-data')); ?>" class="nav-tab"><?php echo esc_html__('Test Data', 'mec'); ?></a>
    </h2>
    <div class="mec-container">
        <div class="import-content w-clearfix extra">
            <div class="mec-meetup-import">
                <form id="mec_meetup_import_form" action="<?php echo esc_url($this->main->get_full_url()); ?>" method="POST">
                    <h3><?php esc_html_e('Import from Meetup', 'mec'); ?></h3>
                    <p class="description"><?php esc_html_e('This will import all your meetup events into MEC.', 'mec'); ?></p>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_meetup_api_key"><?php esc_html_e('Meetup API Key', 'mec'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_meetup_api_key" name="ix[meetup_api_key]" value="<?php echo (isset($ix_options['meetup_api_key']) ? esc_attr($ix_options['meetup_api_key']) : ''); ?>" />
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_ix_meetup_group_url"><?php esc_html_e('Group URL', 'mec'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_ix_meetup_group_url" name="ix[meetup_group_url]" value="<?php echo (isset($ix_options['meetup_group_url']) ? esc_attr($ix_options['meetup_group_url']) : ''); ?>" />
                            <p><?php echo sprintf(esc_html__('just put the slug of your group like %s in %s', 'mec'), '<strong>your-group-slug</strong>', 'https://www.meetup.com/your-group-slug/'); ?></p>
                        </div>
                    </div>
                    <div class="mec-options-fields">
                        <input type="hidden" name="mec-ix-action" value="meetup-import-start" />
                        <button id="mec_ix_meetup_form_button" class="button button-primary mec-button-primary" type="submit"><?php esc_html_e('Start', 'mec'); ?></button>
                    </div>
                </form>
                <?php if($this->action == 'meetup-import-start'): ?>
                <div class="mec-ix-meetup-started">
                    <?php if($this->response['success'] == 0): ?>
                    <div class="mec-error"><?php echo MEC_kses::element($this->response['error']); ?></div>
                    <?php else: ?>
                    <form id="mec_meetup_do_form" action="<?php echo esc_url($this->main->get_full_url()); ?>" method="POST">
                        <div class="mec-xi-meetup-events mec-options-fields">
                            <h4><?php esc_html_e('Meetup Events', 'mec'); ?></h4>
                            <div class="mec-success"><?php echo sprintf(esc_html__('We found %s events for %s group. Please select your desired events to import.', 'mec'), '<strong>'.esc_html($this->response['data']['count']).'</strong>', '<strong>'.esc_html($this->response['data']['title']).'</strong>'); ?></div>
                            <ul class="mec-select-deselect-actions" data-for="#mec_import_meetup_events">
                                <li data-action="select-all"><?php esc_html_e('Select All', 'mec'); ?></li>
                                <li data-action="deselect-all"><?php esc_html_e('Deselect All', 'mec'); ?></li>
                                <li data-action="toggle"><?php esc_html_e('Toggle', 'mec'); ?></li>
                            </ul>
                            <ul id="mec_import_meetup_events">
                                <?php foreach($this->response['data']['events'] as $event): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="m-events[]" value="<?php echo esc_attr($event['id']); ?>" checked="checked" />
                                        <span><?php echo sprintf(esc_html__('Event Title: %s Event Date: %s - %s', 'mec'), '<strong>'.esc_html($event['title']).'</strong>', '<strong>'.esc_html($event['start']).'</strong>', '<strong>'.esc_html($event['end']).'</strong>'); ?></span>
                                    </label>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mec-options-fields">
                            <h4><?php esc_html_e('Import Options', 'mec'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="checkbox" name="ix[import_organizers]" value="1" checked="checked" />
                                    <?php esc_html_e('Import Organizers', 'mec'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="checkbox" name="ix[import_locations]" value="1" checked="checked" />
                                    <?php esc_html_e('Import Locations', 'mec'); ?>
                                </label>
                            </div>
                            <input type="hidden" name="mec-ix-action" value="meetup-import-do" />
                            <input type="hidden" name="ix[meetup_api_key]" value="<?php echo (isset($this->ix['meetup_api_key']) ? esc_attr($this->ix['meetup_api_key']) : ''); ?>" />
                            <input type="hidden" name="ix[meetup_group_url]" value="<?php echo (isset($this->ix['meetup_group_url']) ? esc_attr($this->ix['meetup_group_url']) : ''); ?>" />
                            <button id="mec_ix_meetup_import_do_form_button" class="button button-primary mec-button-primary" type="submit"><?php esc_html_e('Import', 'mec'); ?></button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <?php elseif($this->action == 'meetup-import-do'): ?>
                <div class="mec-ix-meetup-import-do">
                    <?php if($this->response['success'] == 0): ?>
                    <div class="mec-error"><?php echo MEC_kses::element($this->response['error']); ?></div>
                    <?php else: ?>
                    <div class="mec-success"><?php echo sprintf(esc_html__('%s events successfully imported to your website from meetup.', 'mec'), '<strong>'.count($this->response['data']).'</strong>'); ?></div>
                    <div class="info-msg"><strong><?php esc_html_e('Attention', 'mec'); ?>:</strong> <?php esc_html_e("Although we tried our best to make the events completely compatible with MEC but some modification might be needed. We suggest you to edit the imported listings one by one on MEC edit event page and make sure thay're correct.", 'mec'); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>