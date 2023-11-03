export const GLOBAL_FUNC = {
   filterPrice: (objData) =>{
    if(objData.is_check_kythuat && objData.is_check_kythuat == 1){
        objData.price_goc = objData.price_kythuat;
    }else{
        if(objData.percent){
            objData.price_goc;
        }else{
            objData.price_goc = '';
        }
    }
    return objData;
   },
};
