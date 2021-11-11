export default function guest ({ next, store }){
    if(store.getters.authenticated){
        return next({
            name: 'Home'
        })
    }

    return next()
}