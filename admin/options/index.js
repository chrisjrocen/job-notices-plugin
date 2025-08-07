const {render} = wp.element;
import App from './App';

if (document.getElementById('job-notices-react-app')) {
  render(<App/>, document.getElementById('job-notices-react-app'));
}