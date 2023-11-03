import { NativeModules, Platform } from 'react-native'
import { CONFIGS } from '@app/constants';
var Aes = NativeModules.RNAes



async function converLinkVideo(link) {
    try {
        const res = await Aes.convertLinkVideo(link, CONFIGS.SERECT_KEY, CONFIGS.IV);
        console.log('res', res);
        return res;
    } catch (e) {
        console.error(e)
    }
}
function youtube_parser(url) {
    var match = url.match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/);
    return (match && match[7].length == 11) ? match[7] : false;
}

function isYoutubeUrl(youTubeURl) {
    var regExp =/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    return youTubeURl.match(regExp) ? true : false;
}



export {
    converLinkVideo, youtube_parser, isYoutubeUrl
}


