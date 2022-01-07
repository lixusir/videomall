<?php
namespace addons\wanlshop\library\AliyunSdk;
include_once 'aliyun-php-sdk-core/Config.php';

use vod\Request\V20170321 as vod;
use DefaultProfile;
use DefaultAcsClient;

final class Video 
{
	private $client;
	private $accessKeyId;
	private $accessKeySecret;
    private $regionId = 'cn-shanghai';
	
    public function __construct($accessKeyId, $accessKeySecret) {
        try {
            $this->initVodClient($accessKeyId, $accessKeySecret);
        }
        catch(Exception $e) {
            print $e->getMessage();
        }
    }
	
    /**
     * @desc 初始化客户端
     * @param  [type] $accessKeyId
     * @param  [type] $accessKeySecret
     */
    public function initVodClient($accessKeyId, $accessKeySecret) 
	{
		$profile = DefaultProfile::getProfile($this->regionId, $accessKeyId, $accessKeySecret);
		$this->client = new DefaultAcsClient($profile);
    }
	
    /**
     * 获取视频上传地址和凭证
     * @param title 标题
	 * @param fileName 文件名
     * @return CreateUploadVideoResponse 获取视频上传地址和凭证响应数据
     */
    public function createUploadVideo($title, $fileName, $workflowId)
	{
        $request = new vod\CreateUploadVideoRequest();
        $request->setTitle($title);        
        $request->setFileName($fileName); 
		$request->setWorkflowId($workflowId); 
        $request->setAcceptFormat('JSON');
        return $this->client->getAcsResponse($request);
    }
	
	/**
	 * 刷新视频上传凭证
	 * @param videoId 视频ID
	 * @return RefreshUploadVideoResponse 刷新视频上传凭证响应数据
	 */
	public function refreshUploadVideo($videoId) 
	{
	    $request = new vod\RefreshUploadVideoRequest();
	    $request->setVideoId($videoId);
	    $request->setAcceptFormat('JSON');
	    return $this->client->getAcsResponse($request);
	}
	
	
	/**
	 * 获取播放地址
	 * @param videoId 视频ID
	 * @return GetPlayInfoRequest 包含获取播放地址、获取视频播放凭证
	 */
	public function getPlayInfo($videoId) 
	{
	    $request = new vod\GetPlayInfoRequest();
	    $request->setVideoId($videoId);
	    $request->setAcceptFormat('JSON');
	    return $this->client->getAcsResponse($request);
	}
	
	/**
	 * 获取视频信息
	 * @param videoId 视频ID
	 * @return GetVideoInfoResponse 获取视频信息响应数据
	 */
	public function getVideoInfo($videoId) 
	{
		$request = new vod\GetVideoInfoRequest();
		$request->setVideoId($videoId);
		$request->setAcceptFormat('JSON');
		return $this->client->getAcsResponse($request);
	}
	
	/**
     * 删除视频
     * @param videoIds 视频ID
     * @return DeleteVideoRequest 支持批量删除视频；videoIds为传入的视频ID列表，多个用逗号分隔
     */
    public function deleteVideo($videoIds) 
	{
        $request = new vod\DeleteVideoRequest();
        $request->setVideoIds($videoIds);
        $request->setAcceptFormat('JSON');
        return $this->client->getAcsResponse($request);
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}