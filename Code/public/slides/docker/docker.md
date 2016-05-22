# Docker

## Contents

## 왜?
    - 쉽다
    - 관리가 쉽다
    - 배포가 쉽다
    - 환경설정이 쉽다
    - 내 자리에서는 되는데요

## 간단한 시식부터
    - Version control
    
    - maridaDB
    - redis
    - Jenkins
    - nginx
    - redmine

## What is Docker?
    Docker allows you to package an application with all of its dependencies into a standardized unit for software development.
    Docker containers wrap up a piece of software in a complete filesystem that contains everything it needs to run: code, runtime, system tools, system libraries - anything you can install on a server. This guarantees that it will always run the same, regardless of the environment it is running in.

## Lightweight
    Containers running on a single machine all share the same opeating system kernel so they start instantly and make more efficient use of RAM. Images are constructed from layered filesystems so they can share common files, making disk usage and image downloads much more effiecient.

## Open
    Docker containers are based on open standards allowing containers to run on all major Linux distributions and Microsoft oprating systems with supprot for every infrastructure.

## Secure
    Containers isolate applications from each other and the underlying infrastructure while providing an added layer of protection for the application.

## How is this different from virtual machines?
    Containers have similar resource isolation and allocation benefits as virtual machines but a different archiectural approach allows them to be much more portable and efficient.

### Virtual Machines
    Each virtual machine includes the application, the necessary binaries and libraries and an entire guest operating system - all of which may be thens of GBs in size.

### Containers
    Containers include the application and all of its dependencies, but share the kernel with other containers. They run isolated process in userspace on the host operating system. They're also not tied to any specific infrastructure - Docker containers run on any computer, on any infrastructure and in any  cloud.

## How does this help you build better software?
    When your app is in Docker containers, you don't have to worry about setting up and maintaining different environments or different tooling for each language. Focus on creating new features, fixing issues and shipping software.

### Accelerate Developre Onboarding
    Stop wasting hours trying to setup developer envirnments, spin up new instances and make copies of production code to run locally. With Docker, you can easily take copies of your environment and run on any new endpoint running Docker.

### Empower Developer Creativity
    The isolation capabilities of Docker containers free developers from the worries of using "approved" language stacks and tooling. Developers can use the best language and tools for their application service without worrying about causing confilict issues.

### Eliminate Environment Inconsistencies
    By packaging up the application with its configs and dependencies together and shipping as a container, the application will always work as designed locally, on another machine, in test or production. No more worries about having to install the same configs into a different environment.

## Easily Share and Collaborate on Applications
    Docker creates a common framework for developers and sysadmins to work together on distributed application

### Distribute and share content
    Store, distribute and manage your Docker images in your Docker Hub with your team. Image updates, changes and history are automatically shared across your organization.

### Simply share your application withe others
    Ship one or many containers to others or downstream service teams without worrying about different environment dependencies creating issues with your application. Ohter team can easily link to or test against your app without having to learn or worry about how it works.

## Ship More Software Faster
    Docker allows you dynamically change your application like never before from adding new capabilities, scaling out services to quickly changing problem areas.

### Ship 7X More
    Docker useres on average ship software 7X more after deploying Docker in their environment. More frequent updates provide more value to your customers faster.

### Quickly Scale
    Docker containers spin up and down in seconds making it easy to scale an application service at any time to satisfy peak customer demand, then just as easily spin down those containers to only use the resources you need when you need it

### Easily Remediate Issues
    Docker make it easy to identify issues and isolate the problem container, quickly roll back to make the necessary changes then push the updated container into production. The isolation between containers make these changes less disruptive than traditional software models.
    


- Traditional virtualisation
- Docker virtualuzation
- Docker Images
- Image infrastructure
