import app from 'flarum/app';

import FacebookSettingsModal from 'facebook/components/FacebookSettingsModal';

app.initializers.add('facebook', () => {
  app.extensionSettings.facebook = () => app.modal.show(new FacebookSettingsModal());
});
