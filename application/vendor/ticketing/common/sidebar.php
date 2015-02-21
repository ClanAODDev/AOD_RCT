<h3>Repositories</h3>
<nav>
  <ul class="nav nav-tabs nav-stacked">
    <?php $repos = GitHub::repositories() ?>
    <?php foreach( $repos as $repo ) : ?>
      <li>
        <a href="#">
          <?php echo $repo->full_name ?>
          <i class="icon-chevron-right"></i>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</nav>