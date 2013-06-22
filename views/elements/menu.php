<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="<?php echo url(); ?>">Time Sheet</a>

            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li><a href="<?php echo url('grindstone'); ?>" onclick="return confirm('Are you sure?')">GrindStone Import</a></li>
                    <li><a href="<?php echo url('redmine'); ?>" onclick="return confirm('Are you sure?')">Redmine Export</a></li>
                    <li><a href="<?php echo url('saasu'); ?>" onclick="return confirm('Are you sure?')">Saasu Export</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>