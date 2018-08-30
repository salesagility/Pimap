## How to contribute to Pimap

### **Code of Conduct**

This project and all community members are expected to uphold the [Pimap Code of Conduct.](CODE_OF_CONDUCT.md)

#### **Bug Reporting**

- **Please do not open a GitHub issue if the bug is a security vulnerability**, and instead email us at security@suitecrm.com. This will be delivered to the product team who handle security issues. Please don't disclose security bugs publicly until they have been handled by the security team.
- Your email will be acknowledged within 24 hours during the business week (Mon  Fri), and you’ll receive a more detailed response to your email within 72 hours during the business week (Mon  Fri) indicating the next steps in handling your report.
- **Ensure the bug was not already reported** - by searching on GitHub under [Issues.](https://github.com/salesagility/Pimap/issues)
- If you're unable to find an open issue that relates to your problem, [open a new one.](https://github.com/salesagility/Pimap/issues/new) Please be sure to follow the issue template as much as possible.
- **Ensure that the bug you are reporting is a core Pimap issue** - and not specific to your individual setup.

#### **Did you fix a bug?**

- To provide a code contribution for an issue you will need to set up your own fork of the Pimap repository, make your code changes, commit the changes and make a Pull Request to the appropriate branch on the Pimap repository.
- Determine which base branch your bug fix should use. Branches are named based on the next minor release version, eg v0.1.x, v0.2.x etc...
- When committing to your individual fix branch, please try and use the following as your commit message 
```Fixed #1234  <the subject of the issue>```. E.g. ```Fixed #1436  inline attachments```.

- If you are new to Writing Commit Messages in git follow the guide [here](https://chris.beams.io/posts/git-commit/)
- After you have made your commits and pushed them up to your forked repository you then create a [Pull Request](https://help.github.com/articles/about-pull-requests/) to be reviewed and merged into the Pimap repository. Make a new Pull Request for each issue you fix – do not combine multiple bugfixes into one Pull Request.
  Ensure that in your Pull Request that the base fork is salesagility/Pimap and base branch is v0.1.x. and the head fork is your repository and the base branch is your unique bugfix branch e.g. fix_1234
  We will automatically reject any Pull Requests made to the wrong branch!
- If you have not signed our CLA [Contributor License Agreement](https://www.clahub.com/agreements/salesagility/Pimap) then your pull request will fail a check and we will be unable to merge it into the project. You will only be required to sign this once.
- When a new Pull Request is opened, Travis CI will test the merging of the origin and upstream branch and update the Pull Request. If this check fails you can review the test results and resolve accordingly. To test prior to making a Pull Request install Codeception via composer into your development environment then cd into the tests directory and run: ```$./vendor/bin/codecept run unit -vvv -d```
- Ensure that you follow the pull request [template](https://github.com/salesagility/Pimap/blob/master/.github/PULL_REQUEST_TEMPLATE.md) as much as possible.
- When committing to your individual feature branch, please try and use the following as your commit message 
```Fixed #1234  <the subject of the issue>```. E.g. ```Fixed #1436  Reports with nested Parentheses are removing parameters```.


#### **Did you create a new feature or enhancement?**

- Changes that can be considered a new feature or enhancement should be made to the **v0.1.x**.
- To contribute a feature to Pimap, similar to providing a Bug Fix, you must create a forked repository of SuiteCRM and set up your git and development environment.
  Once done, create a new branch from **v0.1.x** - and name it relevant to the feature's purpose e.g feature_sort_extension.
  Make sure your commit messages are relevant and descriptive. When ready to submit for review make a Pull Request detailing your feature's functionality.
  Ensure that in your Pull Request that the base fork is **salesagility/Pimap** - and base branch is **v0.1.x** - and the head fork is your repository and the base branch is your feature branch.
  Add any new automated tests to the new feature branch if required e.g new modules or classes.  
- We will review the code and provide feedback within the Pull Request and issues relating to your feature. If the feature is to be included in the core product we will request for the forked repo to have an Issues tab so we can raise any bugs from our testing. This will also allow you to fix those issues using the below commit message format similar to how to submit bug fixes to the v0.1.x branch.
  ```$ git commit m "Feature #1436 Sort Extension"```. You can add an Issues tab to your forked repository via the 'Settings' tab.

Thanks!

SalesAgility Team
