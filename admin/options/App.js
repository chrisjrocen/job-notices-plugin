const useEffect = wp.element.useEffect;
const useState = wp.element.useState;

import {
  Card,
  Button,
  CardHeader,
  CardBody,
  CardFooter,
  TextControl,
  ToggleControl,
  Notice,
  __experimentalText as Text,
  __experimentalHeading as Heading,
  RadioControl,
} from '@wordpress/components';

const App = () => {

  const [enableJobsArchivePage, setEnableJobsArchivePage] = useState(false);
  const [enableRightSidebar, setEnableRightSidebar] = useState(false);
  const [enableLeftSidebar, setEnableLeftSidebar] = useState(false);
  const [invitationHeading, setInvitationHeading] = useState('Job Invitation');
  const [shareText, setShareText] = useState('Check out Whatsapp Channel!');
  const [shareUrl, setShareUrl] = useState('');
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    /**
     * Initialize the options fields with the data received from the REST API
     * endpoint provided by the plugin.
     */
    wp.apiFetch({ path: '/jobs-settings-page/v1/options' })
      .then(data => {
        //Set the new values of the options in the state
        setEnableJobsArchivePage(!!data['enable_jobs_archive_page']);
        setEnableRightSidebar(!!data['enable_jobs_right_sidebar']);
        setEnableLeftSidebar(!!data['enable_jobs_left_sidebar']);
        setInvitationHeading(data['job_notices_invitation_heading'] || 'Job Invitation');
        setShareText(data['job_notices_share_text'] || 'Check out this job!');
        setShareUrl(data['job_notices_share_url'] || '');
        setIsLoading(false);
      })
      .catch(err => {
        setError('Failed to load options');
        setIsLoading(false);
      });
  }, []);

  if (isLoading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <Notice status="error">{error}</Notice>;
  }

  return (
    <Card style={{ marginTop: 20, marginRight: 40 }}>
      <CardHeader>
        <Heading level={4}>Jobs Settings</Heading>
      </CardHeader>
      <CardBody>
        <ToggleControl
          label="Enable Jobs Archive Page"
          checked={enableJobsArchivePage}
          onChange={() => setEnableJobsArchivePage((state) => !state)}
        />
        {enableJobsArchivePage && (
          <ToggleControl
            label="Enable Right Sidebar"
            checked={enableRightSidebar}
            onChange={() => setEnableRightSidebar((state) => !state)}
          />
        )}
        {enableJobsArchivePage && (
          <ToggleControl
            label="Enable Left Sidebar"
            checked={enableLeftSidebar}
            onChange={() => setEnableLeftSidebar((state) => !state)}
          />
        )}
      </CardBody>
      <CardHeader>
        <Heading level={4}>Settings for Job Invitation</Heading>
      </CardHeader>
      <CardBody>
         <TextControl
          label="Heading"
          value={invitationHeading}
          onChange={(value) => setInvitationHeading(value)}
        />
        <TextControl
          label="Share Text"
          value={shareText}
          onChange={(value) => setShareText(value)}
        />
        <TextControl
          label="Share URL"
          value={shareUrl}
          onChange={(value) => setShareUrl(value)}
        />
      </CardBody>
      <CardFooter>
        <Text>Save Jobs Options</Text>
        <Button
          variant="primary"
          onClick={() => {

            wp.apiFetch({
              path: '/jobs-settings-page/v1/options',
              method: 'POST',
              data: {
                'enable_jobs_archive_page': enableJobsArchivePage,
                'enable_jobs_right_sidebar': enableRightSidebar,
                'enable_jobs_left_sidebar': enableLeftSidebar,
                'job_notices_invitation_heading': invitationHeading,
                'job_notices_share_text': shareText,
                'job_notices_share_url': shareUrl,
              },
            }).then(data => {
              alert('Options saved successfully! Page will refresh on OK');
              document.getElementById('wp-admin-canonical').href = 'edit.php?post_type=jobs&page=job-notices-options';
              window.location.replace(document.getElementById('wp-admin-canonical').href);
            });

          }}>Save Settings
        </Button>
      </CardFooter>
    </Card>
  );

};
export default App;