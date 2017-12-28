<nav class="navbar navbar-default navbar-static-top" role="navigation" style="min-height:100px; margin-bottom: 0; background-color:rgb(3,29,78)">
	
	<div class="container-fluid">
	
		<div class="navbar-header">
		
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
			
			<a class="navbar-brand" href="/toolkit/">
				<img src="/assets/img/Nemours_white.png" alt="Nemours Biomedical Research" id="logo" style="margin-right:2em;">
			</a>
		</div>	
	
		<div class="navbar-left">
			<h1>
				<?php echo SITENAME; ?>
			</h1>
		</div>

		<ul class="nav navbar-top-links navbar-right">
				
			<li>
				<a href="#" title="search">			
					<i class="fa fa-search-plus fa-fw"></i>		
				</a>
			</li> 
			
			<?php if(ADM === true): ?>			
			<li>
				<a href="admin" title="toolkit admin">
					<i class="fa fa-cog fa-fw"></i>
				</a>						
			</li> 
			<?php endif; ?>
							
		</ul>
	</div>

	
	<div class="navbar-default sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav in" id="side-menu">
				<li>
					<a class="active" href="/toolkit/"><i class="fa fa-wrench fa-fw"></i> Toolkit</a>
				</li>
				
				<li>
					<a href="http://www.nemoursresearch.org/snap/"><i class="fa fa-flask fa-fw"></i> SNAP</a>
				</li>
				<li>
					<a href="https://www.nemoursresearch.org/tk2/"><i class="fa fa-clock-o fa-fw"></i> Timekeeper</a>
				</li> 
				<li>
					<a href="https://apps.nemoursresearch.org/redcap"><i class="fa fa-database fa-fw"></i> REDCap</a>
				</li> 
				<li>
					<a href="http://teamshare/patient/research/default.aspx"><i class="fa fa-briefcase fa-fw"></i> TeamShare: Research</a>
				</li>
			</ul>
		</div>
	</div>
	
	
	
	

</nav>