// main list component
const selectComponent = {
    props: ['url'],
	data: () => ({
        currencyList : [],
        singleSelected: "EUR" ,
        loadingSingle: true,
        multiSelected: ["EUR"],
        loadingMultiple: true
	}),
	methods: {
        getList() {
            const $this = this;
            fetch(currencyListMainEndpoint)
                .then( res => { return res.json(); } )
                .then( data => {$this.currencyList = data[0].rates;} )
                .catch( err => { console.errror(error) } )
		},
        getCurrencyByCode(target){
           
            const im = document.getElementById(target);
            im.src= singleMainEndpoint +"/"+this.singleSelected 
        },
        getMultiCurrencyImage(target){        
          const im = document.getElementById(target);
            im.src= multipleMainEndpoint+"/"+this.multiSelected.join(`,`)
        }
	},
	mounted() { 
        this.getList('currencies');
        this.getCurrencyByCode("singleImage");
        this.getMultiCurrencyImage("multiImage")
	},
}

// create vue3
const app = Vue.createApp(selectComponent);
 app.config.compilerOptions.delimiters = ['${', '}']
// mount
app.mount("#app");