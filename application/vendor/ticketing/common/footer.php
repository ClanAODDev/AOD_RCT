<script src="js/prettyprint.min.js"></script>
<script>
  prettyPrint();
  
  var template = '&lt;iframe frameborder="0" style="width: 100%;border: none" height="610" allowtransparency="true" scrolling="no" src="<?php echo rtrim(SITE_URL, '/') ?>/embed.php?owner=__OWNER__&amp;repo=__REPO__&amp;token=<?php echo VALIDATING_HASH ?>"&gt;&lt;/iframe&gt;';
  
  var repo_select = document.getElementById('repo'),
      embed_block = document.getElementById('embed-code'),
      preview = document.getElementById('preview');
  
  repo_select.onchange = function() {
    var owner, repo,
        pieces = this.value.split('/'),
        parsed_template = template;
    
    owner = pieces[0];
    repo = pieces[1];
    
    parsed_template = parsed_template.replace('__OWNER__', owner);
    parsed_template = parsed_template.replace('__REPO__', repo);
    
    embed_block.innerHTML = parsed_template;
    prettyPrint();
  }
</script>
</body>
</html>