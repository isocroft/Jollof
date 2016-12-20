;(function(w, n){

	/*
	 * Setup UpUps' Service Worker and Cachaeble Assets (PWA new style)
	 */

	if(typeof n.serviceWorker != 'undefined'){

		if(!w.UpUp){
			return;
		}

		w.UpUp.start({
			'content-url':'/',
			 assets:[
			 	'/public/offline.html?type=service-worker',
			 	'/public/css/bootstrap-theme.mincss',
			 	'/public/css/bootstrap.min.css',
			 	'/public/js/jquery-1.10.2.js',
			 	'/public/js/bootstrap.js',
			 	'/public/js/browsengine.js'
			 ],
			'service-worker-url': '/public/upup.sw.min.js'
		});
	}

	/* 
	 * Setup Application Cache as fallback from lack of native
	 * support for Service-Workers (PWA old style)
	 */

	else if(typeof w.applicationCache != 'undefined'){

	 		if(!w.Cachr){
	 			return;
	 		}

	 		w.Cachr.go();
		
	 }

}(this, this.navigator));