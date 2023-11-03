
function stripBOM(content) {
    try {
        content = content.toString()
        if (content.charCodeAt(0) === 0xFEFF) {
            content = content.slice(1)
        }
        return content;
    } catch (error) {
        throw error
    }

}

export const jsonHelper = {
    stripBOM
}