<!-- HTML template with placeholders for page numbers -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Template</title>
	<style>
        @page { margin: 80px 20px 60px 20px; }
             header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; 
			          font-family: 'DejaVu Serif', serif;
                      line-height: 1;
		              font-size: 11px !important;
			        }
             footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 50px; 
			          font-family: 'DejaVu Serif', serif;
                      line-height: 1;
		              font-size: 11px !important;
					}
	   .page:after { content: counter(page, decimal); }
	   
	  
		
		.center {
                text-align: center;
              }
             .center img {
                display: block;
              }		  
	  
	   main {
		  font-family: Helvetica;
          line-height: 1;
		  font-size: 11px !important;
				  }
		
		
	  table{
		border-collapse: collapse;
		border-spacing: 0;
		width: 100%;
		max-width: 100%;
		
	   }
      
	 
	   
	</style>
</head>
<body>
   <header>
	              <div  style="border:1px solid; padding:1px;">
                          
								<div class="text-center" style="padding-bottom:5px;">
								   <div style="font-size:14px;">{{config('app.name')}}</div>
                                 </div>
					
                            <div style="clear:both;"></div>
                      </div> 
   </header>
   <footer>
	    <div style="font-size:10px; width: 100%;">
		   <hr style="margin:0;padding:0;"/>
		   <div style="text-align: right;">
			   {{ __('Print Date').' : '.date('d/m/Y H:i') }}
		   </div>
		   <div class="page" style="text-align: center;">
			   Page 
		   </div>
		   <div style="margin-top:10px;text-align: center;">
			  {{$branch_data}}
		   </div>
	   
       </div>
   </footer>
   <main style="margin-top:10px;margin-bottom:10px;">    
	 @if($template->cat_name=='')
	    <div style="margin-top:5px;margin-bottom:5px;background-color: #eeeeee;border:0px;text-align:center;"><b style="min-width:100px;display:inline-block;margin:5px;font-size:14px;">{{$template->test_name}}</b></div>
     @else 
	    <div style="margin-top:5px;margin-bottom:5px;background-color: #eeeeee;border:0px;text-align:center;"><b style="min-width:100px;display:inline-block;margin:5px;font-size:14px;">{{$template->cat_name.' : '.$template->test_name}}</b></div>
	 @endif
	<div style="margin-top:5px;margin-bottom:5px;width:100%;max-width:100%;">
	  {!! $descrip !!}
	</div>
    </main>
	
</body>
</html>



