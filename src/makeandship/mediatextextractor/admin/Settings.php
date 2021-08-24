<div class="wrap">
    <?php
use makeandship\mediatextextractor\admin\HtmlUtils;
use makeandship\mediatextextractor\Constants;
use makeandship\mediatextextractor\settings\SettingsManager;

if (!empty($_POST)) {

    // get incoming options
    $acf_field      = trim($_POST[Constants::OPTION_ACF_FIELD_NAME]);
    $acf_hash_field = trim($_POST[Constants::OPTION_ACF_HASH_FIELD_NAME]);

    // save incoming options
    SettingsManager::get_instance()->set(Constants::OPTION_ACF_FIELD_NAME, $acf_field);
    SettingsManager::get_instance()->set(Constants::OPTION_ACF_HASH_FIELD_NAME, $acf_hash_field);
}
?>
    <h1>Media Text Extractor</h1>
    <div id="poststuff">
        <form method="post" action="">
            <div id="config-container" class="postbox">
                <h2 class="handle"><span>1. Configure your media fields</span></h2>
                <div class="inside acf-fields -left">
                    <div class="media-text-extractor-container">
                        <div class="media-text-extractor-row">
                            <div class="twocol">
                                <label for="">Version</label>
                            </div>
                            <div class="tencol last">
                                <?php echo Constants::VERSION; ?>
                            </div>
                        </div>
                        <div class="media-text-extractor-row">
                            <div class="twocol">
                                <label for="">Extractor</label>
                            </div>
                            <div class="tencol last">
                                <?php echo SettingsManager::get_instance()->get_extractor_name(); ?>
                            </div>
                        </div>
                        <?php
echo HtmlUtils::render_field(
    'ACF field for extracted text',
    Constants::OPTION_ACF_FIELD_NAME,
    array(
        'class'       => '',
        'placeholder' => '',
        'value'       => 'extracted_text',
    )
);

echo HtmlUtils::render_field(
    'ACF field for extracted hash',
    Constants::OPTION_ACF_HASH_FIELD_NAME,
    array(
        'class'       => '',
        'placeholder' => '',
        'value'       => 'extracted_hash',
    )
);

echo HtmlUtils::render_buttons([
    array(
        'value' => 'Save',
        'name'  => 'media_text_extractor_save_button',
        'class' => 'button-primary',
        'id'    => 'save',
    ),
]);
?>
                        <span id="mapping-spinner" class="acf-spinner"></span>
                        <span id="mapping-messages"></span>
                    </div>
                </div>
            </div>
            <div id="indexing-container" class="postbox">
                <h2 class="handle"><span>2. Analyse media</span></h2>
                <div class="inside acf-fields -left">
                    <div class="media-text-extractor-container">
                        <?php
echo HtmlUtils::render_buttons([
    array(
        'value' => 'Analyse media',
        'name'  => 'media_text_extractor_analyse_media_button',
        'class' => 'button',
        'id'    => 'analyse-media',
    ),
    array(
        'value' => 'Resume analysing media',
        'name'  => 'media_text_extractor_resume_analysing_media_button',
        'class' => 'button',
        'id'    => 'resume-analysing-media',
    ),
]);
?>
                        <span id="extract-spinner" class="acf-spinner"></span>
                        <span id="extract-messages"></span>
                    </div>
                </div>
            </div>
            <div id="indexing-individual-container" class="postbox">
                <h2 class="handle"><span>3. Analyse individual media</span></h2>
                <div class="inside acf-fields -left">
                    <div class="media-text-extractor-container">
                        <?php
echo HtmlUtils::render_field(
    'Post or Attachment ID',
    Constants::OPTION_ACF_POST_OR_ATTACHMENT_ID,
    array(
        'class'       => '',
        'placeholder' => '54372',
        'value'       => '',
    )
);
?>
                        <?php
echo HtmlUtils::render_buttons([
    array(
        'value' => 'Analyse media',
        'name'  => 'media_text_extractor_analyse_individual_media_button',
        'class' => 'button',
        'id'    => 'analyse-individual-media',
    ),
]);
?>
                        <span id="extract-individual-spinner" class="acf-spinner"></span>
                        <span id="extract-individual-messages"></span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>