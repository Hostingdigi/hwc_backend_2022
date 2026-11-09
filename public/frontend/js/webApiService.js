class WebApiService {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
    }

    disableBtns(btnClass) {
        for (const i of btnClass) $('.'+i+":visible").each(function(i){
            $(this).prop('disabled',true);
        });
    }

    enableBtns(btnClass) {
        for (const i of btnClass) $('.'+i+":visible").each(function(i){
            $(this).prop('disabled',false);
        });
    }

    hideElements(elements) {
        for (const i of elements){
            if (i.includes("#")) {
                $(i).hide();
            }else{
                $(i+":visible").each(function(i){
                    $(this).hide();
                });
            }
        }
    }

    showElements(elements) {
        for (const i of elements){
            if (i.includes("#")) {
                $(i).show();
            }else{
                $(i).each(function(i){
                    $(this).show();
                });
            }
        }
    }

    get(endpoint, data = {}, beforeSendCallback = null) {
        return $.ajax({
            url: this.baseUrl + endpoint,
            type: 'GET',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: beforeSendCallback || function () {
                console.log('Sending request...');
            }
        });
    }

    post(endpoint, data = {}, beforeSendCallback = null) {
        return $.ajax({
            url: this.baseUrl + endpoint,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: beforeSendCallback || function () {
                // console.log('Sending request...');
            }
        });
    }
}

const webApi = new WebApiService();