# Job

[Job](/src/Job.php) implementation.

A job consists of one [Fetcher](./job/fetcher.md) and one or more [Task](./job/task.md)s.

A job processes one entity at the time.

* The job uses the fetcher to get a streamable tmp file with data.
* The tmp file is sent to each task on job init.
* The job uses the current non finished task to process one entity at the time.

## List of Tasks

The [TaskList](/src/Job/TaskList.php) class holds all [task](./job/task.md)s.

## Job Factory

In your project, you implement a [job factory](/doc/example/jobfactory.md) class which creates a new job using dependecy injection.

## Example

We have a products.csv file. The file contains all data for both simple and configurable products.

Then:

* The **Job** ImportProducts consists of two tasks:
    - **Task** Import Simple
    - **Task** Import Configurable