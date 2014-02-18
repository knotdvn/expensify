jQuery(document).ready(function($) {
// Handler for .ready() called.

	//load datepicker once
	var dp = $('#datepicker');
	dp.datepicker({ defaultDate: "+3y" });
	dp.datepicker( "option", "dateFormat", "yy-mm-dd" );


	// do we have auth already?
	if( $.cookie('ExpensifyAUTH')){
		stage2();
	//if we have proper expire times there should be no old cookies
	}//end if authed
	

	//this builds an <li> from a transactionList object
	//im only including the three data fields creatable in this exercise
	//date //merchant name //amount
	function buildItem(elem){
		var liString = "";
		liString += '<li><span class="date">' + elem.created.toString() + '</span>';
		liString += '<span class="amount">' + elem.amount.toString() + '</span>';
		liString += '<span class="merchant">' + elem.merchant.toString() + '</span>';
		liString += '</li>';
		return liString;

	}//end build item



	//click handler for submit button sign on credentials
	$('#expensify_credentials').on('click', '#authenticator', function(){
		var formEmail = $('#expensify_credentials #email_address').val();
		var formPassword = $('#expensify_credentials #password').val();
		//minimal input checking
		if( formEmail === '' || formPassword === ''){
			alert("Please fill in the required fields");
			return;
		}
		//api creds
		var data = {
			command: 'Authenticate',
			partnerUserID: formEmail,
			partnerUserSecret: formPassword,
		};

		//make the auth request
		var myREQ = $.ajax({
			type: "POST",
			url: 'proxy.php',
			data: data,
			error: function(jqXHR, textStatus, errorThrown){
				alert('Something is Amiss Please Try Again ');
				console.log(jqXHR.statusCode());
				console.log(textStatus);
				console.log(jqXHR);
			},
			success: function(data, textStatus, jqXHR){
				if( $.cookie('ExpensifyAUTH')){
					stage2();
				}else{
					relay = $.parseJSON(data);
					alert(relay.msg);
				}
			},
			datatype: 'json'
			
		});//end request		
	});//end click handler


	//clears the stage of auth and shows transactions
	function stage2(){

		$('#auth').hide();
		//clear list
		$('#transaction_list').html('');
		$('#transaction').show();
		$('#loading').show();
		//make a Get Request for the transaction

		var auth = $.cookie('ExpensifyAUTH');
		var data = {
			command: 'Get',
			authToken: auth
		};

		//make the Get Transaction request
		var myREQ = $.ajax({
			type: "POST",
			url: 'proxy.php',
			data: data,
			error: function(jqXHR, textStatus, errorThrown){
				alert('Something is Amiss Please Try Again ');
				console.log(jqXHR.statusCode());
				console.log(textStatus);
				console.log(jqXHR);
			},
			success: function(data, textStatus, jqXHR){
				
				relay = $.parseJSON(data);
				if(relay.error =="Auth"){
					alert(relay.msg);
					stage1();
				}else if(relay.error == "true"){
					alert(relay.msg);
				}else if(relay.error == "false"){
					//logic for building table
					var dataBlock = $.parseJSON(relay.msg);
					//if this was truly production code
					//i'd feed this json into angularJS
					//and have a ton of nice front end data search and sort features
					//but for now the most recent 10 transactions get spit out
					$(dataBlock.transactionList).each(function(index, element){
						if( index >= 10){
							//kill loop after 10
							return false;
						}else{
							$('#transaction_list').append(buildItem(element));
						}//end if transaction item
					});//end for each 
					//table built
					$('#loading').hide();
				}//end if relay no error
			},
			datatype: 'json'
		});//end request
	}//end function stage2


	//initial stage, ask for auth, hide table
	function stage1(){
		$('#transaction').hide();
		$('#auth').show();
	}//end funciton stage 1


	//click handler for create transaction button
	$('#create').on('click','#create_button', function(){
		var merchantData = $('#merchant_input').val();
		var dateData = $('#datepicker').val();
		var amountData = $('#amount_input').val();

		//do we have input
		if(merchantData !== '' && dateData !== '' && amountData !== ''){
			var auth = $.cookie('ExpensifyAUTH');
			var data = {
				command: 'CreateTransaction',
				authToken: auth,
				merchant: merchantData,
				amount: amountData,
				date: dateData
			};

			//make the Get Transaction request
			var myREQ = $.ajax({
				type: "POST",
				url: 'proxy.php',
				data: data,
				error: function(jqXHR, textStatus, errorThrown){
					alert('Something is Amiss Please Try Again ');
					console.log(jqXHR.statusCode());
					console.log(textStatus);
					console.log(jqXHR);
				},
				success: function(data, textStatus, jqXHR){				
					var relay = $.parseJSON(data);
					if(relay.error =="Auth"){
						alert(relay.msg);
						stage1();
					}else if(relay.error == "true"){
						alert(relay.msg);
					}else if(relay.error == "false"){
						var dataBlock = $.parseJSON(relay.msg);
						alert("Transaction#:" + dataBlock.transactionID + " has been created and added to the Transaction List");
						$('#transaction_list').append( buildItem( dataBlock.transactionList[0] ) );
					}//end if relay no error
				},
				datatype: 'json'			
			});//end request
		}else{
			alert('Please Provide Adequate Transaction Information');
		}//end else has input
	});//end create click handler

	//click handler for refresh
	$('#create').on('click', '#refresh_button', function(){
		//reload stage 2
		stage2();
	});//end click handcler refresh

	//click handler for reauthenticate
	$('#create').on('click', '#reauthenticate_button', function(){
		//kill cookie
		$.removeCookie('ExpensifyAUTH');
		//reload stage 1
		stage1();
	});//end click handcler refresh

});//end doc ready