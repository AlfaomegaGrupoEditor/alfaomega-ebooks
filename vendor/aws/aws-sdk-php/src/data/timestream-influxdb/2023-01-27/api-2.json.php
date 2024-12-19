<?php
// This file was auto-generated from sdk-root/src/data/timestream-influxdb/2023-01-27/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2023-01-27', 'endpointPrefix' => 'timestream-influxdb', 'jsonVersion' => '1.0', 'protocol' => 'json', 'ripServiceName' => 'timestream-influxdb', 'serviceAbbreviation' => 'Timestream InfluxDB', 'serviceFullName' => 'Timestream InfluxDB', 'serviceId' => 'Timestream InfluxDB', 'signatureVersion' => 'v4', 'signingName' => 'timestream-influxdb', 'targetPrefix' => 'AmazonTimestreamInfluxDB', 'uid' => 'timestream-influxdb-2023-01-27', ], 'operations' => [ 'CreateDbInstance' => [ 'name' => 'CreateDbInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateDbInstanceInput', ], 'output' => [ 'shape' => 'CreateDbInstanceOutput', ], 'errors' => [ [ 'shape' => 'ServiceQuotaExceededException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], 'idempotent' => true, ], 'CreateDbParameterGroup' => [ 'name' => 'CreateDbParameterGroup', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateDbParameterGroupInput', ], 'output' => [ 'shape' => 'CreateDbParameterGroupOutput', ], 'errors' => [ [ 'shape' => 'ServiceQuotaExceededException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], 'idempotent' => true, ], 'DeleteDbInstance' => [ 'name' => 'DeleteDbInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteDbInstanceInput', ], 'output' => [ 'shape' => 'DeleteDbInstanceOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], 'idempotent' => true, ], 'GetDbInstance' => [ 'name' => 'GetDbInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetDbInstanceInput', ], 'output' => [ 'shape' => 'GetDbInstanceOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'GetDbParameterGroup' => [ 'name' => 'GetDbParameterGroup', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetDbParameterGroupInput', ], 'output' => [ 'shape' => 'GetDbParameterGroupOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'ListDbInstances' => [ 'name' => 'ListDbInstances', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListDbInstancesInput', ], 'output' => [ 'shape' => 'ListDbInstancesOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'ListDbParameterGroups' => [ 'name' => 'ListDbParameterGroups', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListDbParameterGroupsInput', ], 'output' => [ 'shape' => 'ListDbParameterGroupsOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'ListTagsForResource' => [ 'name' => 'ListTagsForResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTagsForResourceRequest', ], 'output' => [ 'shape' => 'ListTagsForResourceResponse', ], 'errors' => [ [ 'shape' => 'ResourceNotFoundException', ], ], ], 'TagResource' => [ 'name' => 'TagResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'TagResourceRequest', ], 'errors' => [ [ 'shape' => 'ResourceNotFoundException', ], ], 'idempotent' => true, ], 'UntagResource' => [ 'name' => 'UntagResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UntagResourceRequest', ], 'errors' => [ [ 'shape' => 'ResourceNotFoundException', ], ], 'idempotent' => true, ], 'UpdateDbInstance' => [ 'name' => 'UpdateDbInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateDbInstanceInput', ], 'output' => [ 'shape' => 'UpdateDbInstanceOutput', ], 'errors' => [ [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], 'idempotent' => true, ], ], 'shapes' => [ 'AccessDeniedException' => [ 'type' => 'structure', 'required' => [ 'message', ], 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'AllocatedStorage' => [ 'type' => 'integer', 'box' => true, 'max' => 16384, 'min' => 20, ], 'Arn' => [ 'type' => 'string', 'max' => 1011, 'min' => 1, 'pattern' => 'arn:aws[a-z\\-]*:timestream\\-influxdb:[a-z0-9\\-]+:[0-9]{12}:(db\\-instance|db\\-parameter\\-group)/[a-zA-Z0-9]{3,64}', ], 'Boolean' => [ 'type' => 'boolean', 'box' => true, ], 'Bucket' => [ 'type' => 'string', 'max' => 64, 'min' => 2, 'pattern' => '[^_"][^"]*', ], 'ConflictException' => [ 'type' => 'structure', 'required' => [ 'message', 'resourceId', 'resourceType', ], 'members' => [ 'message' => [ 'shape' => 'String', ], 'resourceId' => [ 'shape' => 'String', ], 'resourceType' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'CreateDbInstanceInput' => [ 'type' => 'structure', 'required' => [ 'name', 'password', 'dbInstanceType', 'vpcSubnetIds', 'vpcSecurityGroupIds', 'allocatedStorage', ], 'members' => [ 'name' => [ 'shape' => 'DbInstanceName', ], 'username' => [ 'shape' => 'Username', ], 'password' => [ 'shape' => 'Password', ], 'organization' => [ 'shape' => 'Organization', ], 'bucket' => [ 'shape' => 'Bucket', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'vpcSubnetIds' => [ 'shape' => 'VpcSubnetIdList', ], 'vpcSecurityGroupIds' => [ 'shape' => 'VpcSecurityGroupIdList', ], 'publiclyAccessible' => [ 'shape' => 'Boolean', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'tags' => [ 'shape' => 'RequestTagMap', ], 'port' => [ 'shape' => 'Port', ], ], ], 'CreateDbInstanceOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', 'vpcSubnetIds', ], 'members' => [ 'id' => [ 'shape' => 'DbInstanceId', ], 'name' => [ 'shape' => 'DbInstanceName', ], 'arn' => [ 'shape' => 'Arn', ], 'status' => [ 'shape' => 'Status', ], 'endpoint' => [ 'shape' => 'String', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], 'vpcSubnetIds' => [ 'shape' => 'VpcSubnetIdList', ], 'publiclyAccessible' => [ 'shape' => 'Boolean', ], 'vpcSecurityGroupIds' => [ 'shape' => 'VpcSecurityGroupIdList', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'availabilityZone' => [ 'shape' => 'String', ], 'secondaryAvailabilityZone' => [ 'shape' => 'String', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'influxAuthParametersSecretArn' => [ 'shape' => 'String', ], ], ], 'CreateDbParameterGroupInput' => [ 'type' => 'structure', 'required' => [ 'name', ], 'members' => [ 'name' => [ 'shape' => 'DbParameterGroupName', ], 'description' => [ 'shape' => 'CreateDbParameterGroupInputDescriptionString', ], 'parameters' => [ 'shape' => 'Parameters', ], 'tags' => [ 'shape' => 'RequestTagMap', ], ], ], 'CreateDbParameterGroupInputDescriptionString' => [ 'type' => 'string', 'max' => 500, 'min' => 0, ], 'CreateDbParameterGroupOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', ], 'members' => [ 'id' => [ 'shape' => 'DbParameterGroupId', ], 'name' => [ 'shape' => 'DbParameterGroupName', ], 'arn' => [ 'shape' => 'Arn', ], 'description' => [ 'shape' => 'String', ], 'parameters' => [ 'shape' => 'Parameters', ], ], ], 'DbInstanceId' => [ 'type' => 'string', 'max' => 64, 'min' => 3, 'pattern' => '[a-zA-Z0-9]+', ], 'DbInstanceIdentifier' => [ 'type' => 'string', 'max' => 64, 'min' => 3, 'pattern' => '[a-zA-Z0-9]+', ], 'DbInstanceName' => [ 'type' => 'string', 'max' => 40, 'min' => 3, 'pattern' => '[a-zA-z][a-zA-Z0-9]*(-[a-zA-Z0-9]+)*', ], 'DbInstanceSummary' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', ], 'members' => [ 'id' => [ 'shape' => 'DbInstanceId', ], 'name' => [ 'shape' => 'DbInstanceName', ], 'arn' => [ 'shape' => 'Arn', ], 'status' => [ 'shape' => 'Status', ], 'endpoint' => [ 'shape' => 'String', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], ], ], 'DbInstanceSummaryList' => [ 'type' => 'list', 'member' => [ 'shape' => 'DbInstanceSummary', ], ], 'DbInstanceType' => [ 'type' => 'string', 'enum' => [ 'db.influx.medium', 'db.influx.large', 'db.influx.xlarge', 'db.influx.2xlarge', 'db.influx.4xlarge', 'db.influx.8xlarge', 'db.influx.12xlarge', 'db.influx.16xlarge', ], ], 'DbParameterGroupId' => [ 'type' => 'string', 'max' => 64, 'min' => 3, 'pattern' => '[a-zA-Z0-9]+', ], 'DbParameterGroupIdentifier' => [ 'type' => 'string', 'max' => 64, 'min' => 3, 'pattern' => '[a-zA-Z0-9]+', ], 'DbParameterGroupName' => [ 'type' => 'string', 'max' => 64, 'min' => 3, 'pattern' => '[a-zA-z][a-zA-Z0-9]*(-[a-zA-Z0-9]+)*', ], 'DbParameterGroupSummary' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', ], 'members' => [ 'id' => [ 'shape' => 'DbParameterGroupId', ], 'name' => [ 'shape' => 'DbParameterGroupName', ], 'arn' => [ 'shape' => 'Arn', ], 'description' => [ 'shape' => 'String', ], ], ], 'DbParameterGroupSummaryList' => [ 'type' => 'list', 'member' => [ 'shape' => 'DbParameterGroupSummary', ], ], 'DbStorageType' => [ 'type' => 'string', 'enum' => [ 'InfluxIOIncludedT1', 'InfluxIOIncludedT2', 'InfluxIOIncludedT3', ], ], 'DeleteDbInstanceInput' => [ 'type' => 'structure', 'required' => [ 'identifier', ], 'members' => [ 'identifier' => [ 'shape' => 'DbInstanceIdentifier', ], ], ], 'DeleteDbInstanceOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', 'vpcSubnetIds', ], 'members' => [ 'id' => [ 'shape' => 'DbInstanceId', ], 'name' => [ 'shape' => 'DbInstanceName', ], 'arn' => [ 'shape' => 'Arn', ], 'status' => [ 'shape' => 'Status', ], 'endpoint' => [ 'shape' => 'String', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], 'vpcSubnetIds' => [ 'shape' => 'VpcSubnetIdList', ], 'publiclyAccessible' => [ 'shape' => 'Boolean', ], 'vpcSecurityGroupIds' => [ 'shape' => 'VpcSecurityGroupIdList', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'availabilityZone' => [ 'shape' => 'String', ], 'secondaryAvailabilityZone' => [ 'shape' => 'String', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'influxAuthParametersSecretArn' => [ 'shape' => 'String', ], ], ], 'DeploymentType' => [ 'type' => 'string', 'enum' => [ 'SINGLE_AZ', 'WITH_MULTIAZ_STANDBY', ], ], 'Duration' => [ 'type' => 'structure', 'required' => [ 'durationType', 'value', ], 'members' => [ 'durationType' => [ 'shape' => 'DurationType', ], 'value' => [ 'shape' => 'DurationValueLong', ], ], ], 'DurationType' => [ 'type' => 'string', 'enum' => [ 'hours', 'minutes', 'seconds', 'milliseconds', ], ], 'DurationValueLong' => [ 'type' => 'long', 'box' => true, 'min' => 0, ], 'GetDbInstanceInput' => [ 'type' => 'structure', 'required' => [ 'identifier', ], 'members' => [ 'identifier' => [ 'shape' => 'DbInstanceIdentifier', ], ], ], 'GetDbInstanceOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', 'vpcSubnetIds', ], 'members' => [ 'id' => [ 'shape' => 'DbInstanceId', ], 'name' => [ 'shape' => 'DbInstanceName', ], 'arn' => [ 'shape' => 'Arn', ], 'status' => [ 'shape' => 'Status', ], 'endpoint' => [ 'shape' => 'String', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], 'vpcSubnetIds' => [ 'shape' => 'VpcSubnetIdList', ], 'publiclyAccessible' => [ 'shape' => 'Boolean', ], 'vpcSecurityGroupIds' => [ 'shape' => 'VpcSecurityGroupIdList', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'availabilityZone' => [ 'shape' => 'String', ], 'secondaryAvailabilityZone' => [ 'shape' => 'String', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'influxAuthParametersSecretArn' => [ 'shape' => 'String', ], ], ], 'GetDbParameterGroupInput' => [ 'type' => 'structure', 'required' => [ 'identifier', ], 'members' => [ 'identifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], ], ], 'GetDbParameterGroupOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', ], 'members' => [ 'id' => [ 'shape' => 'DbParameterGroupId', ], 'name' => [ 'shape' => 'DbParameterGroupName', ], 'arn' => [ 'shape' => 'Arn', ], 'description' => [ 'shape' => 'String', ], 'parameters' => [ 'shape' => 'Parameters', ], ], ], 'InfluxDBv2Parameters' => [ 'type' => 'structure', 'members' => [ 'fluxLogEnabled' => [ 'shape' => 'Boolean', ], 'logLevel' => [ 'shape' => 'LogLevel', ], 'noTasks' => [ 'shape' => 'Boolean', ], 'queryConcurrency' => [ 'shape' => 'InfluxDBv2ParametersQueryConcurrencyInteger', ], 'queryQueueSize' => [ 'shape' => 'InfluxDBv2ParametersQueryQueueSizeInteger', ], 'tracingType' => [ 'shape' => 'TracingType', ], 'metricsDisabled' => [ 'shape' => 'Boolean', ], 'httpIdleTimeout' => [ 'shape' => 'Duration', ], 'httpReadHeaderTimeout' => [ 'shape' => 'Duration', ], 'httpReadTimeout' => [ 'shape' => 'Duration', ], 'httpWriteTimeout' => [ 'shape' => 'Duration', ], 'influxqlMaxSelectBuckets' => [ 'shape' => 'InfluxDBv2ParametersInfluxqlMaxSelectBucketsLong', ], 'influxqlMaxSelectPoint' => [ 'shape' => 'InfluxDBv2ParametersInfluxqlMaxSelectPointLong', ], 'influxqlMaxSelectSeries' => [ 'shape' => 'InfluxDBv2ParametersInfluxqlMaxSelectSeriesLong', ], 'pprofDisabled' => [ 'shape' => 'Boolean', ], 'queryInitialMemoryBytes' => [ 'shape' => 'InfluxDBv2ParametersQueryInitialMemoryBytesLong', ], 'queryMaxMemoryBytes' => [ 'shape' => 'InfluxDBv2ParametersQueryMaxMemoryBytesLong', ], 'queryMemoryBytes' => [ 'shape' => 'InfluxDBv2ParametersQueryMemoryBytesLong', ], 'sessionLength' => [ 'shape' => 'InfluxDBv2ParametersSessionLengthInteger', ], 'sessionRenewDisabled' => [ 'shape' => 'Boolean', ], 'storageCacheMaxMemorySize' => [ 'shape' => 'InfluxDBv2ParametersStorageCacheMaxMemorySizeLong', ], 'storageCacheSnapshotMemorySize' => [ 'shape' => 'InfluxDBv2ParametersStorageCacheSnapshotMemorySizeLong', ], 'storageCacheSnapshotWriteColdDuration' => [ 'shape' => 'Duration', ], 'storageCompactFullWriteColdDuration' => [ 'shape' => 'Duration', ], 'storageCompactThroughputBurst' => [ 'shape' => 'InfluxDBv2ParametersStorageCompactThroughputBurstLong', ], 'storageMaxConcurrentCompactions' => [ 'shape' => 'InfluxDBv2ParametersStorageMaxConcurrentCompactionsInteger', ], 'storageMaxIndexLogFileSize' => [ 'shape' => 'InfluxDBv2ParametersStorageMaxIndexLogFileSizeLong', ], 'storageNoValidateFieldSize' => [ 'shape' => 'Boolean', ], 'storageRetentionCheckInterval' => [ 'shape' => 'Duration', ], 'storageSeriesFileMaxConcurrentSnapshotCompactions' => [ 'shape' => 'InfluxDBv2ParametersStorageSeriesFileMaxConcurrentSnapshotCompactionsInteger', ], 'storageSeriesIdSetCacheSize' => [ 'shape' => 'InfluxDBv2ParametersStorageSeriesIdSetCacheSizeLong', ], 'storageWalMaxConcurrentWrites' => [ 'shape' => 'InfluxDBv2ParametersStorageWalMaxConcurrentWritesInteger', ], 'storageWalMaxWriteDelay' => [ 'shape' => 'Duration', ], 'uiDisabled' => [ 'shape' => 'Boolean', ], ], ], 'InfluxDBv2ParametersInfluxqlMaxSelectBucketsLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersInfluxqlMaxSelectPointLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersInfluxqlMaxSelectSeriesLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersQueryConcurrencyInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 256, 'min' => 0, ], 'InfluxDBv2ParametersQueryInitialMemoryBytesLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersQueryMaxMemoryBytesLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersQueryMemoryBytesLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersQueryQueueSizeInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 256, 'min' => 0, ], 'InfluxDBv2ParametersSessionLengthInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 2880, 'min' => 1, ], 'InfluxDBv2ParametersStorageCacheMaxMemorySizeLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersStorageCacheSnapshotMemorySizeLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersStorageCompactThroughputBurstLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersStorageMaxConcurrentCompactionsInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 64, 'min' => 0, ], 'InfluxDBv2ParametersStorageMaxIndexLogFileSizeLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersStorageSeriesFileMaxConcurrentSnapshotCompactionsInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 64, 'min' => 0, ], 'InfluxDBv2ParametersStorageSeriesIdSetCacheSizeLong' => [ 'type' => 'long', 'box' => true, 'max' => 1000000000000, 'min' => 0, ], 'InfluxDBv2ParametersStorageWalMaxConcurrentWritesInteger' => [ 'type' => 'integer', 'box' => true, 'max' => 256, 'min' => 0, ], 'Integer' => [ 'type' => 'integer', 'box' => true, ], 'InternalServerException' => [ 'type' => 'structure', 'required' => [ 'message', ], 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'exception' => true, 'fault' => true, 'retryable' => [ 'throttling' => false, ], ], 'ListDbInstancesInput' => [ 'type' => 'structure', 'members' => [ 'nextToken' => [ 'shape' => 'NextToken', ], 'maxResults' => [ 'shape' => 'MaxResults', ], ], ], 'ListDbInstancesOutput' => [ 'type' => 'structure', 'required' => [ 'items', ], 'members' => [ 'items' => [ 'shape' => 'DbInstanceSummaryList', ], 'nextToken' => [ 'shape' => 'NextToken', ], ], ], 'ListDbParameterGroupsInput' => [ 'type' => 'structure', 'members' => [ 'nextToken' => [ 'shape' => 'NextToken', ], 'maxResults' => [ 'shape' => 'MaxResults', ], ], ], 'ListDbParameterGroupsOutput' => [ 'type' => 'structure', 'required' => [ 'items', ], 'members' => [ 'items' => [ 'shape' => 'DbParameterGroupSummaryList', ], 'nextToken' => [ 'shape' => 'NextToken', ], ], ], 'ListTagsForResourceRequest' => [ 'type' => 'structure', 'required' => [ 'resourceArn', ], 'members' => [ 'resourceArn' => [ 'shape' => 'Arn', ], ], ], 'ListTagsForResourceResponse' => [ 'type' => 'structure', 'members' => [ 'tags' => [ 'shape' => 'ResponseTagMap', ], ], ], 'LogDeliveryConfiguration' => [ 'type' => 'structure', 'required' => [ 's3Configuration', ], 'members' => [ 's3Configuration' => [ 'shape' => 'S3Configuration', ], ], ], 'LogLevel' => [ 'type' => 'string', 'enum' => [ 'debug', 'info', 'error', ], ], 'MaxResults' => [ 'type' => 'integer', 'box' => true, 'max' => 100, 'min' => 1, ], 'NextToken' => [ 'type' => 'string', 'min' => 1, ], 'Organization' => [ 'type' => 'string', 'max' => 64, 'min' => 1, ], 'Parameters' => [ 'type' => 'structure', 'members' => [ 'InfluxDBv2' => [ 'shape' => 'InfluxDBv2Parameters', ], ], 'union' => true, ], 'Password' => [ 'type' => 'string', 'max' => 64, 'min' => 8, 'pattern' => '[a-zA-Z0-9]+', 'sensitive' => true, ], 'Port' => [ 'type' => 'integer', 'box' => true, 'max' => 65535, 'min' => 1024, ], 'RequestTagMap' => [ 'type' => 'map', 'key' => [ 'shape' => 'TagKey', ], 'value' => [ 'shape' => 'TagValue', ], 'max' => 200, 'min' => 1, ], 'ResourceNotFoundException' => [ 'type' => 'structure', 'required' => [ 'message', 'resourceId', 'resourceType', ], 'members' => [ 'message' => [ 'shape' => 'String', ], 'resourceId' => [ 'shape' => 'String', ], 'resourceType' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'ResponseTagMap' => [ 'type' => 'map', 'key' => [ 'shape' => 'TagKey', ], 'value' => [ 'shape' => 'TagValue', ], 'max' => 200, 'min' => 0, ], 'S3Configuration' => [ 'type' => 'structure', 'required' => [ 'bucketName', 'enabled', ], 'members' => [ 'bucketName' => [ 'shape' => 'S3ConfigurationBucketNameString', ], 'enabled' => [ 'shape' => 'Boolean', ], ], ], 'S3ConfigurationBucketNameString' => [ 'type' => 'string', 'max' => 63, 'min' => 3, 'pattern' => '[0-9a-z]+[0-9a-z\\.\\-]*[0-9a-z]+', ], 'ServiceQuotaExceededException' => [ 'type' => 'structure', 'required' => [ 'message', ], 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'Status' => [ 'type' => 'string', 'enum' => [ 'CREATING', 'AVAILABLE', 'DELETING', 'MODIFYING', 'UPDATING', 'DELETED', 'FAILED', 'UPDATING_DEPLOYMENT_TYPE', 'UPDATING_INSTANCE_TYPE', ], ], 'String' => [ 'type' => 'string', ], 'TagKey' => [ 'type' => 'string', 'max' => 128, 'min' => 1, ], 'TagKeys' => [ 'type' => 'list', 'member' => [ 'shape' => 'TagKey', ], 'max' => 200, 'min' => 1, ], 'TagResourceRequest' => [ 'type' => 'structure', 'required' => [ 'resourceArn', 'tags', ], 'members' => [ 'resourceArn' => [ 'shape' => 'Arn', ], 'tags' => [ 'shape' => 'RequestTagMap', ], ], ], 'TagValue' => [ 'type' => 'string', 'max' => 256, 'min' => 0, ], 'ThrottlingException' => [ 'type' => 'structure', 'required' => [ 'message', ], 'members' => [ 'message' => [ 'shape' => 'String', ], 'retryAfterSeconds' => [ 'shape' => 'Integer', ], ], 'exception' => true, 'retryable' => [ 'throttling' => false, ], ], 'TracingType' => [ 'type' => 'string', 'enum' => [ 'log', 'jaeger', ], ], 'UntagResourceRequest' => [ 'type' => 'structure', 'required' => [ 'resourceArn', 'tagKeys', ], 'members' => [ 'resourceArn' => [ 'shape' => 'Arn', ], 'tagKeys' => [ 'shape' => 'TagKeys', ], ], ], 'UpdateDbInstanceInput' => [ 'type' => 'structure', 'required' => [ 'identifier', ], 'members' => [ 'identifier' => [ 'shape' => 'DbInstanceIdentifier', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], ], ], 'UpdateDbInstanceOutput' => [ 'type' => 'structure', 'required' => [ 'id', 'name', 'arn', 'vpcSubnetIds', ], 'members' => [ 'id' => [ 'shape' => 'DbInstanceId', ], 'name' => [ 'shape' => 'DbInstanceName', ], 'arn' => [ 'shape' => 'Arn', ], 'status' => [ 'shape' => 'Status', ], 'endpoint' => [ 'shape' => 'String', ], 'port' => [ 'shape' => 'Port', ], 'dbInstanceType' => [ 'shape' => 'DbInstanceType', ], 'dbStorageType' => [ 'shape' => 'DbStorageType', ], 'allocatedStorage' => [ 'shape' => 'AllocatedStorage', ], 'deploymentType' => [ 'shape' => 'DeploymentType', ], 'vpcSubnetIds' => [ 'shape' => 'VpcSubnetIdList', ], 'publiclyAccessible' => [ 'shape' => 'Boolean', ], 'vpcSecurityGroupIds' => [ 'shape' => 'VpcSecurityGroupIdList', ], 'dbParameterGroupIdentifier' => [ 'shape' => 'DbParameterGroupIdentifier', ], 'availabilityZone' => [ 'shape' => 'String', ], 'secondaryAvailabilityZone' => [ 'shape' => 'String', ], 'logDeliveryConfiguration' => [ 'shape' => 'LogDeliveryConfiguration', ], 'influxAuthParametersSecretArn' => [ 'shape' => 'String', ], ], ], 'Username' => [ 'type' => 'string', 'max' => 64, 'min' => 1, 'sensitive' => true, ], 'ValidationException' => [ 'type' => 'structure', 'required' => [ 'message', 'reason', ], 'members' => [ 'message' => [ 'shape' => 'String', ], 'reason' => [ 'shape' => 'ValidationExceptionReason', ], ], 'exception' => true, ], 'ValidationExceptionReason' => [ 'type' => 'string', 'enum' => [ 'FIELD_VALIDATION_FAILED', 'OTHER', ], ], 'VpcSecurityGroupId' => [ 'type' => 'string', 'max' => 64, 'min' => 0, 'pattern' => 'sg-[a-z0-9]+', ], 'VpcSecurityGroupIdList' => [ 'type' => 'list', 'member' => [ 'shape' => 'VpcSecurityGroupId', ], 'max' => 5, 'min' => 1, ], 'VpcSubnetId' => [ 'type' => 'string', 'max' => 64, 'min' => 0, 'pattern' => 'subnet-[a-z0-9]+', ], 'VpcSubnetIdList' => [ 'type' => 'list', 'member' => [ 'shape' => 'VpcSubnetId', ], 'max' => 3, 'min' => 1, ], ],];
