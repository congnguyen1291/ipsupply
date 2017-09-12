window.fileDropUpload = {
	version: '1.0',
	loaded: false, 
	el : '',
	maxfiles : 1 ,
	maxfilesize : 1 ,//2 MBs
	paramname : '',
	url : '',
	uploadFinished : function(i,file,response){},
	error : function(err, file) {
			switch(err) {
				case 'BrowserNotSupported':
					showMessage('Your browser does not support HTML5 file uploads!');
					break;
				case 'TooManyFiles':
					alert('Too many files! Please select 5 at most! (configurable)');
					break;
				case 'FileTooLarge':
					alert(file.name+' is too large! Please upload files up to 2mb (configurable).');
					break;
				default:
					break;
			}
		},
	beforeEach : function(file){
			if(!file.type.match(/^image\//)){
				alert('Only images are allowed!');
				return false;
			}
		},
	uploadStarted : function(i, file, len){},
	progressUpdated : function(i, file, progress) {},

	getVersion: function() {
		return this.version;
	},
	getLoaded: function() {
		return this.loaded;
	},
	setContainer: function(el) {
		this.el = el;
	},
	getContainer: function() {
		return this.el;
	},
	setMaxFiles: function(maxfiles) {
		this.maxfiles = maxfiles;
	},
	getMaxFiles: function() {
		return this.maxfiles;
	},
	setMaxFileSize: function(maxfilesize) {
		this.maxfilesize = maxfilesize;
	},
	getMaxFileSize: function() {
		return this.maxfilesize;
	},
	setParamName: function(paramname) {
		this.paramname = paramname;
	},
	getParamName: function() {
		return this.paramname;
	},
	setUrl: function(url) {
		this.url = url;
	},
	getUrl: function() {
		return this.url;
	},
	setUploadFinished: function(uploadFinished) {
		this.uploadFinished = uploadFinished;
	},
	getUploadFinished: function(i,file,response) {
		return this.uploadFinished(i,file,response);
	},
	setError: function(error) {
		this.error = error;
	},
	getError: function(err, file) {
		return this.error(err, file);
	},
	setBeforeEach: function(beforeEach) {
		this.beforeEach = beforeEach;
	},
	getBeforeEach: function(file) {
		return this.beforeEach(file);
	},
	setUploadStarted: function(uploadStarted) {
		this.uploadStarted = uploadStarted;
	},
	getUploadStarted: function(i, file, len) {
		return this.uploadStarted(i, file, len);
	},
	setProgressUpdated: function(progressUpdated) {
		this.progressUpdated = progressUpdated;
	},
	getProgressUpdated: function(i, file, progress) {
		return this.progressUpdated(i, file, progress);
	},

	configure: function(option_) {
		/*this.el = ((typeof(option_.el) === 'undefined') ? '.btnUploadHtml5' : option_.el),
		this.maxfiles = ((typeof(option_.maxfiles) === 'undefined') ? 1 : option_.maxfiles),
		this.maxfilesize = ((typeof(option_.maxfilesize) === 'undefined') ? 1048576 *2 : 1048576*option_.maxfilesize),//2 MBs
		this.paramname =  ((typeof(option_.paramname) === 'undefined') ? '' : option_.paramname),
		this.url = ((typeof(option_.url) === 'undefined') ? '' : option_.url);*/
		if(typeof(option_.el) !== 'undefined'){
			this.el = option_.el;
		}
		if(typeof(option_.maxfiles) !== 'undefined'){
			this.maxfiles = option_.maxfiles;
		}
		if(typeof(option_.maxfilesize) !== 'undefined'){
			this.maxfilesize = option_.maxfilesize;
		}
		if(typeof(option_.paramname) !== 'undefined'){
			this.paramname = option_.paramname;
		}
		if(typeof(option_.url) !== 'undefined'){
			this.url = option_.url;
		}
		if(option_.uploadFinished && typeof(option_.uploadFinished) === 'function'){
			this.uploadFinished = option_.uploadFinished;
		}
		if(option_.error && typeof(option_.error) === 'function'){
			this.error = option_.error;
		}
		if(option_.beforeEach && typeof(option_.beforeEach) === 'function'){
			this.beforeEach = option_.beforeEach;
		}
		if(option_.uploadStarted && typeof(option_.uploadStarted) === 'function'){
			this.uploadStarted = option_.uploadStarted;
		}
		if(option_.progressUpdated && typeof(option_.progressUpdated) === 'function'){
			this.progressUpdated = option_.progressUpdated;
		}

		$(fileDropUpload.getContainer()).filedrop({
			paramname:fileDropUpload.getParamName(),	
			maxfiles: fileDropUpload.getMaxFiles(),
			maxfilesize: fileDropUpload.getMaxFileSize(),
			url: fileDropUpload.getUrl(),	
			uploadFinished:function(i,file,response){
				var data = response;
				if(data.constructor === String){
					data = $.parseJSON(data);
				}
				fileDropUpload.getUploadFinished(i,file,response);
			},
			error: function(err, file) {
				fileDropUpload.getError(err, file);
			},
			beforeEach: function(file){
				fileDropUpload.getBeforeEach(file);
			},
			uploadStarted:function(i, file, len){
				fileDropUpload.getUploadStarted(i, file, len);
			},
			progressUpdated: function(i, file, progress) {
				fileDropUpload.getProgressUpdated(i, file, progress);
			} 
		});
	},

};
