import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import saveConfig from 'flarum/utils/saveConfig';

export default class FacebookSettingsModal extends Modal {
  constructor(...args) {
    super(...args);

    this.appId = m.prop(app.config['facebook.app_id'] || '');
    this.appSecret = m.prop(app.config['facebook.app_secret'] || '');
  }

  className() {
    return 'FacebookSettingsModal Modal--small';
  }

  title() {
    return 'Facebook Settings';
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form">
          <div className="Form-group">
            <label>App ID</label>
            <input className="FormControl" value={this.appId()} oninput={m.withAttr('value', this.appId)}/>
          </div>

          <div className="Form-group">
            <label>App Secret</label>
            <input className="FormControl" value={this.appSecret()} oninput={m.withAttr('value', this.appSecret)}/>
          </div>

          <div className="Form-group">
            <Button
              type="submit"
              className="Button Button--primary FacebookSettingsModal-save"
              loading={this.loading}>
              Save Changes
            </Button>
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();

    this.loading = true;

    saveConfig({
      'facebook.app_id': this.appId(),
      'facebook.app_secret': this.appSecret()
    }).then(
      () => this.hide(),
      () => {
        this.loading = false;
        m.redraw();
      }
    );
  }
}
