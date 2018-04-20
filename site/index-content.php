    <!-- start hero-header -->
    <div class="hero" style="background-image:url('<?=SITEFOLDERSL;?>/images/hero-header/01.jpg');">
      <div class="container">

        <h1>your future starts here now</h1>
        <p>Finding your next job or career more 1000+ availabilities</p>

        <div class="main-search-form-wrapper">

          <form>

            <div class="form-holder">
              <div class="row gap-0">

                <div class="col-xss-6 col-xs-6 col-sm-6">
                  <input class="form-control" placeholder="Looking for job" />
                </div>

                <div class="col-xss-6 col-xs-6 col-sm-6">
                  <input class="form-control" placeholder="Place to work" />
                </div>

              </div>

            </div>

            <div class="btn-holder">
              <button class="btn"><i class="ion-android-search"></i></button>
            </div>

          </form>

        </div>


      </div>
    </div>
    <!-- end hero-header -->
    
    <div class="pt-80 pb-80">
      <div class="container">
        <div class="row">

          <div class="col-lg-12 col-md-12">

            <div class="section-title">

              <h2 class="text-left text-center-sm"><?=$languages['header_latest_ads'];?></h2>
              
            </div>
              
            <?php list_latest_ads($count = 6); ?>
            
        </div>
      </div>
    </div>