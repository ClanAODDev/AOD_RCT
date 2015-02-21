<?php
include 'global.php';

define('PAGE_TITLE', "Manage Clients");
include APP_ROOT . '/common/header.php';

$user_repos = GitHub::repositories();
$org_repos = GitHub::organization_repos();

?>
  <div class="container">    
    <header class="form-horizontal">
      <div class="control-group">
        <label class="control-label" for="repo">Choose a Repository</label>
        
        <div class="controls">
          <select id="repo">
            <option value=""></option>
            
            <optgroup label="<?php echo $user_repos[0]->owner->login ?>">
              <?php foreach( $user_repos as $repo ) : ?>
                <option id="<?php echo $repo->full_name ?>"><?php echo $repo->full_name ?></option>
              <?php endforeach ?>
            </optgroup>
            
            <?php foreach( $org_repos as $org_login => $repos ) : ?>
              <optgroup label="<?php echo $org_login ?>">
                <?php foreach( $repos as $repo ) : ?>
                  <option id="<?php echo $repo->full_name ?>"><?php echo $repo->full_name ?></option>
                <?php endforeach ?>
              </optgroup>
            <?php endforeach ?>
            
          </select>
        </div>
      </div>
    </header>
    
    <section>
      <h3>Embed Code</h3>
      <pre id="embed-code" class="prettyprint linenums"><code>// Choose a Repo Above</code></pre>
      
      
      <h3>Preview</h3>
      
      <iframe id="preview" frameborder="0" style="width: 100%;border: none" height="610" allowtransparency="true" scrolling="no" src="<?php echo rtrim(SITE_URL, '/') ?>/embed.php?owner=__PREVIEW__&amp;repo=__NOT_A_REAL_FORM__&amp;token=<?php echo VALIDATING_HASH ?>"></iframe>
    </section>
  </div>
<?php include APP_ROOT . '/common/footer.php' ?>