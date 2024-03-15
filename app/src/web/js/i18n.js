i18next.init({
    lng: window.gon.language,
    fallbackLng: 'ru',
    resources: {
        en: {
            translation: {
                'document': 'document',
                'document_plural': 'documents',
                'confirmDocumentsResending':  'Do you really want to resend {{count}} $t(document, {"count": {{count}}})?',
                'allDocumentsAreSelected': 'All documents matching search conditions are selected.',
            }
        },
        ru: {
            translation: {
                'document_0': 'документ',
                'document_1': 'документа',
                'document_2': 'документов',
                'confirmDocumentsResending': 'Вы действительно хотите переотравить {{count}} $t(document, {"count": {{count}}})?',
                'allDocumentsAreSelected': 'Выбраны все документы, удовлетворяющие условиям поиска.',
            }
        },
    }
});
